@extends('admin.layout')

@section('title', 'TRIO · نظرة عامة')

@section('content')
<div class="admin-shell">
    @include('admin.sidebar')

    <main class="admin-main">
        <header class="admin-topbar">
            <div>
                <p class="eyebrow">TRIO / PRIVATE STUDIO OPS</p>
                <h1>نظرة عامة على الاستوديو <span>✦</span></h1>
            </div>
            <div style="display:flex; gap:12px; align-items:center;">
                <a class="store-link" href="{{ route('storefront') }}" target="_blank">زيارة المتجر ↗</a>
            </div>
        </header>

        @if(session('success'))
            <div class="alert success">{{ session('success') }}</div>
        @endif
        @if($errors->any())
            <div class="alert error">{{ $errors->first() }}</div>
        @endif

        <!-- شريط الإحصائيات الرئيسي -->
        <section class="metrics">
            <article>
                <span>مبيعات مؤكدة</span>
                <strong>EGP {{ number_format($revenue, 2) }}</strong>
                <small>الطلبات المدفوعة وتحت التنفيذ</small>
            </article>
            <article>
                <span>طلبات نشطة</span>
                <strong>{{ $activeOrders }}</strong>
                <small>جديد، مؤكد، أو في مرحلة الطباعة</small>
            </article>
            <article>
                <span>خامة مطبوعة</span>
                <strong>{{ number_format($soldGrams, 0) }} <em>g</em></strong>
                <small>إجمالي الجرامات بالطلبات المؤكدة</small>
            </article>
            <article>
                <span>تكلفة خامة تقديرية</span>
                <strong>EGP {{ number_format($materialCost, 2) }}</strong>
                <small>المتوسط الفعلي لتكلفة البكرات</small>
            </article>
        </section>

        <!-- بطاقات التنقل السريع للأقسام المنفصلة -->
        <section style="display:grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap:15px; margin-bottom:28px;">
            <a href="{{ route('admin.products.index') }}" style="background:var(--surface); border:1px solid var(--line); border-radius:6px; padding:18px; text-decoration:none; color:inherit; display:flex; justify-content:space-between; align-items:center; transition:transform 0.2s;" onmouseover="this.style.borderColor='var(--lime)'" onmouseout="this.style.borderColor='var(--line)'">
                <div>
                    <span style="font-size:11px; color:var(--muted); font-family:var(--mono); display:block;">الكتالوج</span>
                    <strong style="font-size:18px; color:#fff;">إدارة المنتجات</strong>
                    <small style="color:var(--lime); display:block; margin-top:4px;">{{ $productsCount }} منتج منشور</small>
                </div>
                <span style="font-size:22px;">□</span>
            </a>

            <a href="{{ route('admin.filaments.index') }}" style="background:var(--surface); border:1px solid var(--line); border-radius:6px; padding:18px; text-decoration:none; color:inherit; display:flex; justify-content:space-between; align-items:center; transition:transform 0.2s;" onmouseover="this.style.borderColor='var(--lime)'" onmouseout="this.style.borderColor='var(--line)'">
                <div>
                    <span style="font-size:11px; color:var(--muted); font-family:var(--mono); display:block;">الخامات والبكرات</span>
                    <strong style="font-size:18px; color:#fff;">خامات الفيلمنت</strong>
                    <small style="color:var(--lime); display:block; margin-top:4px;">{{ $filamentsCount }} خامات مسجلة</small>
                </div>
                <span style="font-size:22px;">🧵</span>
            </a>

            <a href="{{ route('admin.orders.index') }}" style="background:var(--surface); border:1px solid var(--line); border-radius:6px; padding:18px; text-decoration:none; color:inherit; display:flex; justify-content:space-between; align-items:center; transition:transform 0.2s;" onmouseover="this.style.borderColor='var(--lime)'" onmouseout="this.style.borderColor='var(--line)'">
                <div>
                    <span style="font-size:11px; color:var(--muted); font-family:var(--mono); display:block;">الطلبات</span>
                    <strong style="font-size:18px; color:#fff;">طلبات المتجر</strong>
                    <small style="color:var(--lime); display:block; margin-top:4px;">{{ $activeOrders }} طلب نشط</small>
                </div>
                <span style="font-size:22px;">◌</span>
            </a>

            <a href="{{ route('admin.custom_quotes.index') }}" style="background:var(--surface); border:1px solid var(--line); border-radius:6px; padding:18px; text-decoration:none; color:inherit; display:flex; justify-content:space-between; align-items:center; transition:transform 0.2s;" onmouseover="this.style.borderColor='var(--lime)'" onmouseout="this.style.borderColor='var(--line)'">
                <div>
                    <span style="font-size:11px; color:var(--muted); font-family:var(--mono); display:block;">ملفات العملاء</span>
                    <strong style="font-size:18px; color:#fff;">تصاميم مخصصة</strong>
                    <small style="color:var(--lime); display:block; margin-top:4px;">{{ $newQuotesCount }} طلب جديد</small>
                </div>
                <span style="font-size:22px;">↗</span>
            </a>
        </section>

        <!-- شبكة ملخص العمل الوارد -->
        <div style="display:grid; grid-template-columns: 1.4fr 1fr; gap:20px;">
            <!-- آخر طلبات المتجر -->
            <section class="panel">
                <div class="panel-title">
                    <div>
                        <p class="eyebrow">RECENT ORDERS</p>
                        <h2>آخر طلبات المتجر</h2>
                    </div>
                    <a href="{{ route('admin.orders.index') }}" style="font-size:11px; color:var(--lime); text-decoration:none;">عرض كل الطلبات ←</a>
                </div>

                <div class="orders-table">
                    @forelse($recentOrders as $order)
                        <div style="background:#09141c; padding:12px; border-radius:6px; border:1px solid var(--line); margin-bottom:8px; display:flex; justify-content:space-between; align-items:center;">
                            <div>
                                <span style="font-family:var(--mono); color:var(--lime); font-size:11px; font-weight:bold;">{{ $order->order_number }}</span>
                                <h4 style="margin:2px 0; font-size:13px;">{{ $order->customer_name }}</h4>
                                <small style="color:var(--muted); font-size:10px;">{{ $order->items->count() }} أصناف · {{ $order->phone }}</small>
                            </div>
                            <div style="text-align:left;">
                                <strong style="font-family:var(--mono); color:#fff; display:block; font-size:13px;">EGP {{ number_format($order->total, 2) }}</strong>
                                <span style="font-size:9px; background:#12242e; color:var(--lime); padding:2px 6px; border-radius:3px; display:inline-block; margin-top:3px;">
                                    {{ $order->status }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="empty-state">لا توجد طلبات واردة بعد.</div>
                    @endforelse
                </div>
            </section>

            <!-- آخر طلبات الطباعة الخاصة -->
            <section class="panel">
                <div class="panel-title">
                    <div>
                        <p class="eyebrow">CUSTOM QUOTES</p>
                        <h2>آخر الملفات المرفوعة</h2>
                    </div>
                    <a href="{{ route('admin.custom_quotes.index') }}" style="font-size:11px; color:var(--lime); text-decoration:none;">عرض الكل ←</a>
                </div>

                <div class="orders-table">
                    @forelse($recentQuotes as $quote)
                        <div style="background:#09141c; padding:12px; border-radius:6px; border:1px solid var(--line); margin-bottom:8px;">
                            <div style="display:flex; justify-content:space-between;">
                                <span style="font-family:var(--mono); color:var(--lime); font-size:10px;">{{ $quote->quote_number }}</span>
                                <strong style="font-size:11px; font-family:var(--mono); color:#fff;">EGP {{ number_format($quote->estimated_price, 2) }}</strong>
                            </div>
                            <h4 style="margin:3px 0; font-size:12px;">{{ $quote->customer_name }}</h4>
                            <small style="color:var(--muted); font-size:10px; display:block;">
                                ملف: {{ $quote->original_filename }} ({{ $quote->filament_name }})
                            </small>
                        </div>
                    @empty
                        <div class="empty-state">لا توجد طلبات تسعير ملفات حتى الآن.</div>
                    @endforelse
                </div>
            </section>
        </div>
    </main>
</div>
@endsection
