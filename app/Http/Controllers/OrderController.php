<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'customer_name' => ['required', 'string', 'max:120'],
            'email' => ['nullable', 'email', 'max:120'],
            'phone' => ['required', 'string', 'regex:/^(\+?20|0)?1[0125][0-9]{8}$/'],
            'address' => ['required', 'string', 'max:500'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'cart' => ['required', 'array', 'min:1'],
            'cart.*.id' => ['required', 'integer'],
            'cart.*.quantity' => ['required', 'integer', 'min:1', 'max:50'],
            'cart.*.color' => ['nullable', 'string', 'max:80'],
        ], [
            'phone.regex' => 'يرجى إدخال رقم هاتف مصري صحيح (مثال: 01282537892).',
            'cart.min' => 'سلة الطلبات فارغة.',
        ]);

        $productIds = collect($data['cart'])->pluck('id')->unique();
        $products = Product::query()->with('filament')->where('is_active', true)->whereIn('id', $productIds)->get()->keyBy('id');

        abort_if($products->count() !== $productIds->count(), 422, 'أحد المنتجات في سلتك لم يعد متاحًا أو تم إخفاؤه.');

        foreach ($data['cart'] as $cartItem) {
            $product = $products->get($cartItem['id']);
            $filament = $product->filament;

            abort_if($filament && ! $filament->is_active, 422, 'خامة أحد المنتجات نفدت من المخزون.');

            $inventory = $filament?->colorInventory() ?? [];
            if ($filament?->hasManagedColorInventory()) {
                abort_unless(
                    collect($inventory)->contains(fn (array $color) => $color['in_stock'] && $color['name'] === ($cartItem['color'] ?? null)),
                    422,
                    'اللون المختار لأحد المنتجات غير متاح حاليًا.'
                );
            }

            $productColors = $product->color_options ?? [];
            if ($productColors !== [] && ($cartItem['color'] ?? null) !== null) {
                abort_unless(
                    in_array($cartItem['color'], $productColors, true),
                    422,
                    'اللون المختار غير مرتبط بهذا المنتج.'
                );
            }
        }

        return DB::transaction(function () use ($data, $products) {
            $items = collect($data['cart'])->map(function (array $cartItem) use ($products) {
                $product = $products->get($cartItem['id']);
                $quantity = (int) $cartItem['quantity'];

                return [
                    'product_id' => $product->id,
                    'name' => $product->name,
                    'color' => $cartItem['color'] ?? null,
                    'unit_price' => $product->price,
                    'weight_grams' => $product->weight_grams,
                    'quantity' => $quantity,
                ];
            });

            $subtotal = $items->sum(fn (array $item) => $item['unit_price'] * $item['quantity']);
            $order = Order::create([
                'order_number' => 'TR-'.strtoupper(Str::random(8)),
                'customer_name' => $data['customer_name'],
                'email' => $data['email'] ?? null,
                'phone' => $data['phone'],
                'address' => $data['address'],
                'notes' => $data['notes'] ?? null,
                'subtotal' => $subtotal,
                'delivery_fee' => 0,
                'total' => $subtotal,
                'status' => 'new',
            ]);
            $order->items()->createMany($items->all());

            return response()->json([
                'message' => 'تم استلام طلبك بنجاح! سنتواصل معك قريبًا لتأكيد التوصيل.',
                'order_number' => $order->order_number,
                'total' => $order->total,
            ], 201);
        });
    }
}
