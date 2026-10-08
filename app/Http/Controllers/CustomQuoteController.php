<?php

namespace App\Http\Controllers;

use App\Models\CustomQuote;
use App\Models\Filament;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CustomQuoteController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'customer_name' => ['required', 'string', 'max:120'],
            'phone' => ['required', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:120'],
            'filament_id' => ['nullable', 'exists:filaments,id'],
            'quality' => ['nullable', 'string', 'max:60'],
            'color' => ['nullable', 'string', 'max:60'],
            'estimated_weight_grams' => ['required', 'numeric', 'min:1'],
            'file' => ['nullable', 'file', 'max:102400'], // max 100MB
            'original_filename' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $filament = ! empty($data['filament_id']) ? Filament::find($data['filament_id']) : Filament::first();
        $filamentName = $filament ? $filament->name : 'PLA Standard';
        $costPerGram = $filament ? (float) $filament->cost_per_gram : 0.675;

        $weight = (float) $data['estimated_weight_grams'];
        $multiplier = 4.0; // standard custom print multiplier
        $estimatedPrice = round($weight * $costPerGram * $multiplier, 2);

        $filePath = null;
        $originalFilename = $data['original_filename'] ?? 'uploaded-model.stl';

        if ($request->hasFile('file')) {
            $uploadedFile = $request->file('file');
            $originalFilename = $uploadedFile->getClientOriginalName();
            $filePath = $uploadedFile->store('custom_quotes', 'public');
        }

        $quote = CustomQuote::create([
            'quote_number' => 'CQ-'.strtoupper(Str::random(7)),
            'customer_name' => $data['customer_name'],
            'phone' => $data['phone'],
            'email' => $data['email'] ?? null,
            'filament_id' => $filament?->id,
            'filament_name' => $filamentName,
            'quality' => $data['quality'] ?? 'Standard 0.20mm',
            'color' => $data['color'] ?? 'Lime Green',
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
            'whatsapp_phone' => '01000000000', // workshop phone placeholder
        ], 201);
    }
}
