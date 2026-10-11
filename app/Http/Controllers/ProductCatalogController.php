<?php

namespace App\Http\Controllers;

use App\Models\Filament;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductCatalogController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query()->with('filament')->where('is_active', true);

        // Search by keyword
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by Filament
        if ($filamentId = $request->input('filament')) {
            $query->where('filament_id', $filamentId);
        }

        // Sort
        match ($request->input('sort')) {
            'price_asc' => $query->orderBy('price', 'asc'),
            'price_desc' => $query->orderBy('price', 'desc'),
            'weight_desc' => $query->orderBy('weight_grams', 'desc'),
            'oldest' => $query->orderBy('id', 'asc'),
            default => $query->orderBy('sort_order')->orderByDesc('id'), // Newest / default
        };

        $products = $query->paginate(12)->withQueryString();
        $filaments = Filament::query()->where('is_active', true)->get();

        return view('catalog.index', [
            'products' => $products,
            'filaments' => $filaments,
            'currentSearch' => $search,
            'currentFilament' => $filamentId,
            'currentSort' => $request->input('sort', 'default'),
        ]);
    }

    public function show(Product $product)
    {
        abort_unless($product->is_active, 404);

        $product->load('filament');

        $relatedProducts = Product::query()->with('filament')
            ->where('is_active', true)
            ->where('id', '!=', $product->id)
            ->take(4)
            ->get();

        return view('catalog.show', [
            'product' => $product,
            'relatedProducts' => $relatedProducts,
        ]);
    }
}
