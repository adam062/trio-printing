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
        ]);

        $costPerGram = Filament::calculateGramCost((float) $data['spool_price'], (float) $data['spool_weight_grams']);

        Filament::create([
            'name' => $data['name'],
            'spool_price' => (float) $data['spool_price'],
            'spool_weight_grams' => (float) $data['spool_weight_grams'],
            'cost_per_gram' => $costPerGram,
            'color_hint' => $data['color_hint'] ?? '#c8f228',
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
        ]);

        $costPerGram = Filament::calculateGramCost((float) $data['spool_price'], (float) $data['spool_weight_grams']);

        $filament->update([
            'name' => $data['name'],
            'spool_price' => (float) $data['spool_price'],
            'spool_weight_grams' => (float) $data['spool_weight_grams'],
            'cost_per_gram' => $costPerGram,
            'color_hint' => $data['color_hint'] ?? $filament->color_hint,
            'is_active' => $request->has('is_active') ? (bool) $request->input('is_active') : $filament->is_active,
        ]);

        return back()->with('success', "تم تحديث سعر وبيانات خامة {$filament->name}.");
    }

    public function destroy(Filament $filament)
    {
        $name = $filament->name;
        $filament->delete();

        return back()->with('success', "تم حذف خامة {$name}.");
    }
}
