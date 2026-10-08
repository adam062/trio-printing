@extends('admin.layout')

@section('title', 'TRIO · إدارة الطلبات والشحن')

@section('content')
<div class="admin-shell">
    @include('admin.sidebar')

    <main class="admin-main">
        <header class="admin-topbar">
            <div>
                <p class="eyebrow">ORDERS & FULFILLMENT</p>
                <h1>طلبات المتجر والشحن <span>◌</span></h1>
            </div>
            <a class="store-link" href="{{ route('admin.dashboard') }}">← عودة لنظرة عامة</a>
        </header>

        @if(session('success'))
            <div class="alert success">{{ session('success') }}</div>
        @endif

        <!-- شريط الفلاتر والبحث -->
        <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px; margin-bottom:20px;">
            <div style="display:flex; gap:6px; flex-wrap:wrap;">
                @php
                    $statuses = [
                        'all' => 'الكل',
                        'new' => 'جديد',
                        'confirmed' => 'مؤكد',
                        'printing' => 'تحت الطباعة',
                        'ready' => 'جاهز',
                        'completed' => 'مكتمل',
                        'cancelled' => 'ملغي'
                    ];
                @endphp
                @foreach($statuses as $key => $label)
                    <a href="{{ route('admin.orders.index', ['status' => $key === 'all' ? null : $key, 'search' => $currentSearch]) }}" 
                       style="padding:6px 12px; border-radius:4px; font-size:11px; text-decoration:none; border:1px solid #273e4b; {{ ($currentStatus === $key || ($key === 'all' && empty($currentStatus))) ? 'background:var(--lime); color:#071019; font-weight:bold;' : 'background:#09151e; color:#dbe6e8;' }}">
                        {{ $label }} ({{ $statusCounts[$key] ?? 0 }})
                    </a>
                @endforeach
            </div>

            <form method="GET" action="{{ route('admin.orders.index') }}" style="display:flex; gap:8px;">
                @if($currentStatus)
                    <input type="hidden" name="status" value="{{ $currentStatus }}">
                @endif
                <input name="search" value="{{ $currentSearch }}" placeholder="بحث برقم الطلب، الاسم، أو الهاتف..." style="background:#09151e; border:1px solid #273e4b; color:#fff; padding:7px 12px; border-radius:4px; font-size:11px; width:220px;" />
                <button type="submit" style="background:#1b2f3a; color:var(--lime); border:1px solid #365565; border-radius:4px; padding:0 12px; font-size:11px; cursor:pointer;">بحث</button>
            </form>
        </div>

        <!-- قائمة الطلبات بالتفاصيل الكاملة -->
        <div class="orders-list">
            @forelse($orders as $order)
                <div class="order-container" style="background:#0a1620; border:1px solid var(--line); border-radius:8px; margin-bottom:16px; padding:18px;">
                    <!-- رأس الطلب -->
                    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px; border-bottom:1px solid rgba(255,255,255,0.08); padding-bottom:14px;">
                        <div style="display:flex; gap:15px; align-items:center;">
                            <span style="font-family:var(--mono); color:var(--lime); font-size:14px; font-weight:bold;">{{ $order->order_number }}</span>
                            <div>
                                <strong style="font-size:15px; display:block;">{{ $order->customer_name }}</strong>
                                <a href="tel:{{ $order->phone }}" style="color:var(--muted); font-size:11px; text-decoration:none; font-family:var(--mono);">📞 {{ $order->phone }}</a>
                            </div>
                        </div>

                        <div style="display:flex; gap:12px; align-items:center;">
                            <div style="text-align:left;">
                                <small style="color:var(--muted); display:block; font-size:10px;">إجمالي الطلب</small>
                                <strong style="font-family:var(--mono); color:var(--lime); font-size:16px;">EGP {{ number_format($order->total, 2) }}</strong>
                            </div>

                            <!-- تحديث الحالة -->
                            <form method="POST" action="{{ route('admin.orders.status', $order) }}">
                                @csrf
                                @method('PATCH')
                                <select name="status" onchange="this.form.submit()" style="background:#04090e; border:1px solid #334e5c; color:#dce7e5; border-radius:4px; padding:7px 10px; font-size:11px; outline:none; cursor:pointer;">
                                    @foreach(['new'=>'جديد','confirmed'=>'تم التأكيد','paid'=>'مدفوع','printing'=>'تحت الطباعة','ready'=>'جاهز للشحن','completed'=>'تم التسليم','cancelled'=>'ملغي'] as $value=>$label)
                                        <option value="{{ $value }}" @selected($order->status === $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </form>

                            <!-- زر واتساب فوري -->
                            @php
                                $waPhone = preg_replace('/[^0-9]/', '', $order->phone);
                                if(str_starts_with($waPhone, '0')) { $waPhone = '20'.substr($waPhone, 1); }
                                $waText = urlencode("أهلاً {$order->customer_name}، نتواصل معك من استوديو TRIO للطباعة ثلاثية الأبعاد بخصوص طلبك رقم {$order->order_number} بقيمة EGP ".number_format($order->total, 2)." لتأكيد الشحن.");
                            @endphp
                            <a href="https://wa.me/{{ $waPhone }}?text={{ $waText }}" target="_blank" style="background:#25d366; color:#071019; padding:7px 12px; border-radius:4px; text-decoration:none; font-size:11px; font-weight:bold; display:flex; align-items:center; gap:5px;">
                                واتساب ↗
                            </a>
                        </div>
                    </div>

                    <!-- تفاصيل المنتجات والعنوان والملاحظات -->
                    <div style="display:grid; grid-template-columns: 1.4fr 1fr; gap:20px; margin-top:14px; font-size:12px;">
                        <div>
                            <p style="font-size:11px; color:var(--muted); margin:0 0 8px;">الأصناف المطلوبة ({{ $order->items->count() }}):</p>
                            <div style="display:grid; gap:6px;">
                                @foreach($order->items as $item)
                                    <div style="background:#050c12; padding:9px 14px; border-radius:4px; display:flex; justify-content:space-between; align-items:center; border:1px solid rgba(255,255,255,0.04);">
                                        <div>
                                            <div style="display:flex; align-items:center; gap:8px;">
                                                <strong style="color:#edf2f1;">{{ $item->name }}</strong>
                                                @if($item->color)
                                                    <span style="background:rgba(74,222,128,0.15); color:var(--lime); border:1px solid rgba(74,222,128,0.3); padding:2px 8px; border-radius:4px; font-size:11px; font-weight:600;">
                                                        اللون: {{ $item->color }}
                                                    </span>
                                                @endif
                                            </div>
                                            <small style="color:var(--muted); display:block; font-size:10px;">وزن القطعة: {{ $item->weight_grams }}g</small>
                                        </div>
                                        <div style="font-family:var(--mono); text-align:left;">
                                            <span style="color:var(--muted);">{{ $item->quantity }} × </span>
                                            <strong style="color:var(--lime);">EGP {{ number_format($item->unit_price, 2) }}</strong>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div style="background:#050c12; padding:14px; border-radius:4px; border:1px dashed #213540;">
                            <p style="margin:0 0 5px; color:var(--muted); font-size:10px;">عنوان التوصيل:</p>
                            <p style="margin:0 0 10px; color:#edf2f1; line-height:1.6;">{{ $order->address }}</p>

                            @if($order->notes)
                                <p style="margin:0 0 4px; color:var(--muted); font-size:10px;">ملاحظات العميل:</p>
                                <p style="margin:0 0 10px; color:#dbe6e8; background:#020508; padding:7px; border-radius:3px; font-size:11px;">{{ $order->notes }}</p>
                            @endif

                            <p style="margin:0; font-size:10px; color:var(--muted); font-family:var(--mono);">
                                وقت الطلب: {{ $order->created_at->format('Y-m-d · H:i') }}
                            </p>
                        </div>
                    </div>
                </div>
            @empty
                <div class="empty-state">لا توجد طلبات مطابقة للفلتر المحدد.</div>
            @endforelse

            <div style="margin-top:20px;">
                {{ $orders->links() }}
            </div>
        </div>
    </main>
</div>
@endsection
