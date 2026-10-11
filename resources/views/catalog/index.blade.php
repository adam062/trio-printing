<!doctype html>
<html lang="ar" dir="rtl">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="theme-color" content="#071019" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>كتالوج منتجات TRIO — 3D Printing Catalog</title>
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Alexandria:wght@400;500;600;700;800&family=DM+Mono:wght@400;500&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="@assetv('trio.css')" />
    <link rel="stylesheet" href="@assetv('store-overrides.css')" />
    <style>
      .catalog-header { width: 100%; max-width: none; margin: 40px 0 30px; padding: 0 clamp(16px, 4vw, 42px); display: flex; justify-content: space-between; align-items: flex-end; flex-wrap: wrap; gap: 20px; }
      .catalog-filters { width: 100%; max-width: none; margin: 0 0 35px; padding: 0 clamp(16px, 4vw, 42px); display: flex; gap: 15px; flex-wrap: wrap; align-items: center; justify-content: space-between; }
      .filter-group { display: flex; gap: 10px; align-items: center; flex-wrap: wrap; }
      .filter-chip { padding: 8px 14px; border-radius: 20px; font-size: 11px; text-decoration: none; border: 1px solid var(--line); color: var(--white); background: #0c1822; transition: all 0.2s; }
      .filter-chip.active, .filter-chip:hover { border-color: var(--lime); color: var(--lime); background: #132717; }
      .sort-select, .search-box { background: #0c1822; border: 1px solid var(--line); color: #fff; padding: 9px 14px; border-radius: 4px; font-size: 12px; outline: none; }
      .search-box { min-width: 240px; }
      .catalog-grid { width: 100%; max-width: none; margin: 0 0 80px; padding: 0 clamp(16px, 4vw, 42px); display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 24px; }
      .product-card-link { text-decoration: none; color: inherit; display: block; position: relative; }
      .pagination-wrap { width: 100%; max-width: none; margin: 0 0 80px; padding: 0 clamp(16px, 4vw, 42px); text-align: center; }
    </style>
  </head>
  <body>
    <div class="noise"></div>
    <div class="glow glow-a"></div><div class="glow glow-b"></div>

    @include('partials.store-header', ['activeNav' => 'catalog'])

    <main>
      <section class="catalog-header">
        <div>
          <span class="kicker">TRIO CATALOG / ALL DROPS</span>
          <h1 style="font-size: clamp(32px, 4vw, 54px); margin-top: 8px;">استكشف جميع منتجات <em>TRIO</em></h1>
          <p style="color:#a6b6bc; font-size:14px; margin-top:10px;">قطع مصممة ومطبوعة بأعلى درجات الدقة لتناسب مكتبك وحياتك اليومية.</p>
        </div>
        <a href="{{ route('storefront') }}" class="text-link">← العودة للرئيسية</a>
      </section>

      <!-- شريط البحث والفلترة والترتيب -->
      <section class="catalog-filters">
        <!-- فلتر نوع الخامة -->
        <div class="filter-group">
          <a href="{{ route('catalog.index', ['sort' => $currentSort, 'search' => $currentSearch]) }}" 
             class="filter-chip {{ empty($currentFilament) ? 'active' : '' }}">
            الكل
          </a>
          @foreach($filaments as $fil)
            <a href="{{ route('catalog.index', ['filament' => $fil->id, 'sort' => $currentSort, 'search' => $currentSearch]) }}" 
               class="filter-chip {{ $currentFilament == $fil->id ? 'active' : '' }}">
              {{ $fil->name }}
            </a>
          @endforeach
        </div>

        <!-- البحث والترتيب -->
        <form method="GET" action="{{ route('catalog.index') }}" style="display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
          @if($currentFilament)
            <input type="hidden" name="filament" value="{{ $currentFilament }}">
          @endif

          <input type="text" name="search" value="{{ $currentSearch }}" placeholder="ابحث باسم المنتج..." class="search-box" />

          <select name="sort" onchange="this.form.submit()" class="sort-select">
            <option value="default" @selected($currentSort === 'default')>الترتيب: الأحدث</option>
            <option value="price_asc" @selected($currentSort === 'price_asc')>السعر: من الأقل للأعلى</option>
            <option value="price_desc" @selected($currentSort === 'price_desc')>السعر: من الأعلى للأقل</option>
            <option value="weight_desc" @selected($currentSort === 'weight_desc')>الوزن: الأثقل وزناً</option>
          </select>

          <button type="submit" class="button button-quiet" style="padding: 9px 16px; font-size:11px;">تطبيق</button>
        </form>
      </section>

      <!-- شبكة المنتجات الكاملة -->
      <section class="catalog-grid">
        @php
          $visuals = ['stand', 'headphone', 'planters'];
        @endphp
        @forelse($products as $product)
          @php
            $managedColors = $product->filament?->colorInventory() ?? [];
            $hasManagedInventory = $product->filament?->hasManagedColorInventory() ?? false;
            $isOutOfStock = $product->filament && ! $product->filament->is_active
                || ($hasManagedInventory && ! collect($managedColors)->contains(fn ($color) => $color['in_stock']));
          @endphp
            <article class="product-card product-{{ ['lime', 'blue', 'dark'][$loop->index % 3] }}" style="height:440px;">
            <div class="product-tag">{{ $product->filament?->name ?? '3D PRINTED' }}</div>
            
            <a href="{{ route('catalog.show', $product) }}" style="position:absolute; inset:0; z-index:1;"></a>

            @if($product->image_path)
              <img class="product-uploaded-image" src="@sasset($product->image_path)" alt="{{ $product->name }}" />
            @else
              <div class="product-visual {{ $visuals[$loop->index % 3] }}"><span></span><i></i><b></b></div>
            @endif

            <div class="product-info" style="position:relative; z-index:2; pointer-events:none;">
              <div>
                <h3 style="margin-bottom:4px;">{{ $product->name }}</h3>
                <p style="margin:0; opacity:0.85;">
                  {{ $product->weight_grams }}g
                  @if($product->dimensions) · {{ $product->dimensions }} @endif
                </p>
              </div>
              <strong style="font-size:14px;">EGP {{ number_format((float) $product->price, 2) }}</strong>
            </div>

            <div class="product-card-actions" style="position:relative; z-index:3; display:flex; gap:8px;">
              @if($isOutOfStock)
                <button class="add-button" disabled>نفد المخزون</button>
              @elseif($hasManagedInventory)
                <a class="add-button" href="{{ route('catalog.show', $product) }}">اختار اللون <span>↗</span></a>
              @else
                <button class="add-button" data-id="{{ $product->id }}" data-name="{{ $product->name }}" data-price="{{ $product->price }}" data-image="@if($product->coverImage())@sasset($product->coverImage())@endif">
                  أضف للسلة <span>+</span>
                </button>
              @endif
            </div>
          </article>
        @empty
          <div style="grid-column: 1/-1; text-align:center; padding:90px 20px; color:#8fa1a7;">
            <span style="font-size:40px; display:block; margin-bottom:12px;">◌</span>
            <p>لا توجد منتجات مطابقة للبحث أو الفلتر المحدد حاليًا.</p>
            <a href="{{ route('catalog.index') }}" class="button button-primary" style="margin-top:15px;">عرض كافة المنتجات</a>
          </div>
        @endforelse
      </section>

      @if($products->hasPages())
        <div class="pagination-wrap">
          {{ $products->links() }}
        </div>
      @endif
    </main>

    @include('partials.store-footer')

    <!-- سلة الطلبات المشتركة -->
    @include('partials.cart-popup')

    <div class="backdrop" id="backdrop"></div>
    <div class="toast" id="toast">تمت إضافة القطعة للسلة <span>✓</span></div>

    <script>
      window.TRIO_CONFIG = {
        orderUrl: @json(route('orders.store')),
        csrf: @json(csrf_token()),
        whatsappPhone: '{{ config('app.whatsapp_phone', '201282537892') }}'
      };
    </script>
    <script src="@assetv('trio.js')"></script>
  </body>
</html>
