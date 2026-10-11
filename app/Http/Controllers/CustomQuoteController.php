<?php

namespace App\Http\Controllers;

use App\Models\CustomQuote;
use App\Models\Filament;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CustomQuoteController extends Controller
{
    public function create()
    {
        $filaments = Filament::query()->where('is_active', true)->orderBy('id')->get()
            ->filter(fn (Filament $filament) => ! $filament->hasManagedColorInventory()
                || collect($filament->colorInventory())->contains(fn (array $color) => $color['in_stock']))
            ->values();

        return view('custom-print', [
            'filaments' => $filaments,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'customer_name' => ['required', 'string', 'max:120'],
            'phone' => ['required', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:120'],
            'filament_id' => ['required', 'exists:filaments,id'],
            'quality' => ['nullable', 'string', 'max:60'],
            'color' => ['nullable', 'string', 'max:60'],
            'estimated_weight_grams' => ['required', 'numeric', 'min:1'],
            'file' => ['nullable', 'file', 'extensions:stl,3mf,zip', 'max:102400'], // max 100MB
            'original_filename' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $filament = Filament::query()->where('is_active', true)->find($data['filament_id']);

        abort_if(! $filament, 422, 'الخامة المختارة غير متاحة حاليًا.');

        $inventory = $filament?->colorInventory() ?? [];
        if ($filament->hasManagedColorInventory()) {
            abort_unless(
                collect($inventory)->contains(fn (array $color) => $color['in_stock']),
                422,
                'نفدت ألوان الخامة المختارة حاليًا.'
            );

            abort_unless(
                collect($inventory)->contains(fn (array $color) => $color['in_stock'] && $color['name'] === ($data['color'] ?? null)),
                422,
                'اللون المختار غير متاح حاليًا.'
            );
        }

        $filamentName = $filament->name;
        $costPerGram = (float) $filament->cost_per_gram;

        $weight = (float) $data['estimated_weight_grams'];
        $multiplier = 4.0; // standard custom print multiplier
        $estimatedPrice = round($weight * $costPerGram * $multiplier, 2);

        $filePath = null;
        $originalFilename = $data['original_filename'] ?? 'uploaded-model.stl';

        if ($request->hasFile('file')) {
            $uploadedFile = $request->file('file');
            $originalFilename = $uploadedFile->getClientOriginalName();
            $extension = strtolower($uploadedFile->getClientOriginalExtension());
            $filePath = $uploadedFile->storeAs(
                'custom_quotes',
                Str::uuid().'.'.$extension,
                'public'
            );
        }

        $quote = CustomQuote::create([
            'quote_number' => 'CQ-'.strtoupper(Str::random(7)),
            'customer_name' => $data['customer_name'],
            'phone' => $data['phone'],
            'email' => $data['email'] ?? null,
            'filament_id' => $filament->id,
            'filament_name' => $filamentName,
            'quality' => $data['quality'] ?? 'Standard 0.20mm',
            'color' => $data['color'] ?? 'غير محدد',
            'file_path' => $filePath,
            'original_filename' => $originalFilename,
            'estimated_weight_grams' => $weight,
            'multiplier' => $multiplier,
            'estimated_price' => $estimatedPrice,
            'notes' => $data['notes'] ?? null,
            'status' => 'new',
        ]);

        return response()->json([
            'message' => 'تم استلام طلب التسعير بنجاح. سيقوم فريق TRIO بمراجعة الملف والتواصل معك.',
            'quote_number' => $quote->quote_number,
            'estimated_price' => $quote->estimated_price,
            'whatsapp_phone' => config('app.whatsapp_phone'),
        ], 201);
    }
}
