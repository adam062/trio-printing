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
            <div class="admin-topbar-actions">
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
                <small>تقدير بمتوسط تكلفة الجرام للخامات النشطة</small>
            </article>
        </section>

        <!-- بطاقات التنقل السريع للأقسام المنفصلة -->
        <section class="dashboard-quick-links">
            <a class="dashboard-quick-link" href="{{ route('admin.products.index') }}">
                <div>
                    <span class="link-label">الكتالوج</span>
                    <strong class="link-title">إدارة المنتجات</strong>
                    <small class="link-meta">{{ $productsCount }} منتج منشور</small>
                </div>
                <span class="link-icon">□</span>
            </a>

            <a class="dashboard-quick-link dashboard-filament-link" href="{{ route('admin.filaments.index') }}">
                <div>
                    <span class="link-label">الخامات والبكرات</span>
                    <strong class="link-title">خامات الفيلمنت</strong>
                    <small class="dashboard-filament-summary">
                        <span>{{ $activeFilamentsCount }} متاحة</span>
                        <span>{{ $inactiveFilamentsCount }} متوقفة</span>
                        <span>{{ $filamentsCount }} إجماليًا</span>
                    </small>
                </div>
                <span class="link-icon">🧵</span>
            </a>

            <a class="dashboard-quick-link" href="{{ route('admin.orders.index') }}">
                <div>
                    <span class="link-label">الطلبات</span>
                    <strong class="link-title">طلبات المتجر</strong>
                    <small class="link-meta">{{ $activeOrders }} طلب نشط</small>
                </div>
                <span class="link-icon">◌</span>
            </a>

            <a class="dashboard-quick-link" href="{{ route('admin.custom_quotes.index') }}">
                <div>
                    <span class="link-label">ملفات العملاء</span>
                    <strong class="link-title">تصاميم مخصصة</strong>
                    <small class="link-meta">{{ $newQuotesCount }} طلب جديد</small>
                </div>
                <span class="link-icon">↗</span>
            </a>
        </section>

        <!-- شبكة ملخص العمل الوارد -->
        <div class="dashboard-content-grid">
            <!-- آخر طلبات المتجر -->
            <section class="panel">
                <div class="panel-title">
                    <div>
                        <p class="eyebrow">RECENT ORDERS</p>
                        <h2>آخر طلبات المتجر</h2>
                    </div>
                    <a class="view-all-link" href="{{ route('admin.orders.index') }}">عرض كل الطلبات →</a>
                </div>

                <div class="orders-table">
                    @forelse($recentOrders as $order)
                        <div class="dashboard-item dashboard-order-item">
                            <div class="dashboard-order-info">
                                <span class="order-number">{{ $order->order_number }}</span>
                                <h4 class="customer-name">{{ $order->customer_name }}</h4>
                                <small class="order-meta">{{ $order->items->count() }} أصناف · {{ $order->phone }}</small>
                            </div>
                            <div class="dashboard-order-total">
                                <strong class="total-amount">EGP {{ number_format($order->total, 2) }}</strong>
                                <span class="status-badge">{{ $order->status }}</span>
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
                    <a class="view-all-link" href="{{ route('admin.custom_quotes.index') }}">عرض الكل →</a>
                </div>

                <div class="orders-table">
                    @forelse($recentQuotes as $quote)
                        <div class="dashboard-item">
                            <div class="dashboard-quote-header">
                                <span class="dashboard-quote-number">{{ $quote->quote_number }}</span>
                                <strong class="dashboard-quote-price">EGP {{ number_format($quote->estimated_price, 2) }}</strong>
                            </div>
                            <h4 class="dashboard-quote-name">{{ $quote->customer_name }}</h4>
                            <small class="dashboard-quote-meta">
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