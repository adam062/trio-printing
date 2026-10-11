<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Filament;
use Illuminate\Http\Request;

class FilamentController extends Controller
{
    public function index()
    {
        $filaments = Filament::query()->withCount('products')->orderBy('id')->get();

        return view('admin.filaments.index', [
            'filaments' => $filaments,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'spool_price' => ['required', 'numeric', 'min:1'],
            'spool_weight_grams' => ['required', 'numeric', 'min:50', 'max:10000'],
            'color_hint' => ['nullable', 'string', 'max:30'],
            'colors' => ['nullable', 'array', 'max:30'],
            'colors.*.name' => ['nullable', 'string', 'max:80'],
            'colors.*.in_stock' => ['sometimes', 'boolean'],
        ]);

        $costPerGram = Filament::calculateGramCost((float) $data['spool_price'], (float) $data['spool_weight_grams']);

        Filament::create([
            'name' => $data['name'],
            'spool_price' => (float) $data['spool_price'],
            'spool_weight_grams' => (float) $data['spool_weight_grams'],
            'cost_per_gram' => $costPerGram,
            'color_hint' => $data['color_hint'] ?? '#c8f228',
            'available_colors' => $this->normalizeColors($data['colors'] ?? []) ?: null,
            'is_active' => true,
        ]);

        return back()->with('success', 'تمت إضافة خامة الفيلمنت بنجاح.');
    }

    public function update(Request $request, Filament $filament)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'spool_price' => ['required', 'numeric', 'min:1'],
            'spool_weight_grams' => ['required', 'numeric', 'min:50', 'max:10000'],
            'color_hint' => ['nullable', 'string', 'max:30'],
            'is_active' => ['sometimes', 'boolean'],
            'colors' => ['nullable', 'array', 'max:30'],
            'colors.*.name' => ['nullable', 'string', 'max:80'],
            'colors.*.in_stock' => ['sometimes', 'boolean'],
        ]);

        $costPerGram = Filament::calculateGramCost((float) $data['spool_price'], (float) $data['spool_weight_grams']);
        $colors = $this->normalizeColors($data['colors'] ?? []);
        $availableColors = $colors;

        if ($colors === [] && ! $filament->hasManagedColorInventory()) {
            $availableColors = null;
        }

        $filament->update([
            'name' => $data['name'],
            'spool_price' => (float) $data['spool_price'],
            'spool_weight_grams' => (float) $data['spool_weight_grams'],
            'cost_per_gram' => $costPerGram,
            'color_hint' => $data['color_hint'] ?? $filament->color_hint,
            'available_colors' => $availableColors,
            'is_active' => $request->has('is_active') ? $request->boolean('is_active') : $filament->is_active,
        ]);

        return back()->with('success', "تم تحديث سعر وبيانات خامة {$filament->name}.");
    }

    public function destroy(Filament $filament)
    {
        $name = $filament->name;
        $filament->delete();

        return back()->with('success', "تم حذف خامة {$name}.");
    }

    /**
     * @param  array<int, array{name?: string|null, in_stock?: bool|string|null}>  $colors
     * @return array<int, array{name: string, in_stock: bool}>
     */
    private function normalizeColors(array $colors): array
    {
        return collect($colors)
            ->map(fn (array $color) => [
                'name' => trim((string) ($color['name'] ?? '')),
                'in_stock' => filter_var($color['in_stock'] ?? false, FILTER_VALIDATE_BOOLEAN),
            ])
            ->filter(fn (array $color) => $color['name'] !== '')
            ->unique(fn (array $color) => mb_strtolower($color['name']))
            ->values()
            ->all();
    }
}
