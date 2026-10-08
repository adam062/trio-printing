<?php

namespace App\Http\Controllers;

use App\Models\Filament;
use App\Models\Product;

class StorefrontController extends Controller
{
    public function __invoke()
    {
        return view('storefront', [
            'products' => Product::query()->with('filament')->where('is_active', true)->orderBy('sort_order')->orderByDesc('id')->get(),
            'filaments' => Filament::query()->where('is_active', true)->orderBy('id')->get(),
        ]);
    }
}
