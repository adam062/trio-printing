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

        $filamentStats = Filament::query()
            ->selectRaw('COUNT(*) as total_count')
            ->selectRaw('SUM(CASE WHEN is_active = ? THEN 1 ELSE 0 END) as active_count', [true])
            ->selectRaw('AVG(CASE WHEN is_active = ? THEN cost_per_gram END) as average_active_cost_per_gram', [true])
            ->first();

        $filamentsCount = (int) $filamentStats->total_count;
        $activeFilamentsCount = (int) $filamentStats->active_count;
        $inactiveFilamentsCount = $filamentsCount - $activeFilamentsCount;
        $productsCount = Product::count();
        $newQuotesCount = CustomQuote::query()->where('status', 'new')->count();

        $avgCostPerGram = (float) ($filamentStats->average_active_cost_per_gram ?? 0);
        $materialCost = $soldGrams * (float) $avgCostPerGram;

        return view('admin.dashboard', [
            'recentOrders' => $orders,
            'recentQuotes' => $customQuotes,
            'revenue' => $revenue,
            'activeOrders' => $activeOrders,
            'soldGrams' => $soldGrams,
            'materialCost' => $materialCost,
            'filamentsCount' => $filamentsCount,
            'activeFilamentsCount' => $activeFilamentsCount,
            'inactiveFilamentsCount' => $inactiveFilamentsCount,
            'productsCount' => $productsCount,
            'newQuotesCount' => $newQuotesCount,
        ]);
    }
}
