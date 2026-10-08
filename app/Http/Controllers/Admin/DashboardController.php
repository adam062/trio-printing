<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomQuote;
use App\Models\Filament;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $orders = Order::query()->with('items')->latest()->take(6)->get();
        $customQuotes = CustomQuote::query()->latest()->take(5)->get();

        $completedQuery = Order::query()->whereIn('status', ['paid', 'printing', 'ready', 'completed']);
        $revenue = (float) (clone $completedQuery)->sum('total');
        $activeOrders = Order::query()->whereIn('status', ['new', 'confirmed', 'printing'])->count();

        $soldGrams = (float) OrderItem::query()
            ->whereHas('order', fn ($q) => $q->whereIn('status', ['paid', 'printing', 'ready', 'completed']))
            ->selectRaw('COALESCE(SUM(weight_grams * quantity), 0) as total_weight')
            ->value('total_weight');

        $filamentsCount = Filament::count();
        $productsCount = Product::count();
        $newQuotesCount = CustomQuote::query()->where('status', 'new')->count();

        $avgCostPerGram = Filament::avg('cost_per_gram') ?: 0.675;
        $materialCost = $soldGrams * (float) $avgCostPerGram;

        return view('admin.dashboard', [
            'recentOrders' => $orders,
            'recentQuotes' => $customQuotes,
            'revenue' => $revenue,
            'activeOrders' => $activeOrders,
            'soldGrams' => $soldGrams,
            'materialCost' => $materialCost,
            'filamentsCount' => $filamentsCount,
            'productsCount' => $productsCount,
            'newQuotesCount' => $newQuotesCount,
        ]);
    }
}
