<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class OrderStatusController extends Controller
{
    public function update(Request $request, Order $order)
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(['new', 'confirmed', 'paid', 'printing', 'ready', 'completed', 'cancelled'])],
        ]);

        $order->update($data);

        return back()->with('success', 'تم تحديث حالة الطلب.');
    }
}
