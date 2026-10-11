<!doctype html>
<html lang="ar" dir="rtl">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="theme-color" content="#071019" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    @php
      $inventoryColors = $product->filament?->colorInventory() ?? [];
      $hasManagedColors = $product->filament?->hasManagedColorInventory() ?? false;
      $selectedNames = $product->color_options ?? [];

      if ($hasManagedColors) {
          $filtered = $selectedNames === []
              ? $inventoryColors
              : array_values(array_filter($inventoryColors, fn ($color) => in_array($color['name'], $selectedNames, true)));
          // Never hide every color because of a stale selection.
          $productColors = $filtered === [] ? $inventoryColors : $filtered;
      } else {
          $productColors = collect($selectedNames)->map(fn ($color) => ['name' => $color, 'in_stock' => true])->all();
      }

      $isOutOfStock = ($product->filament && ! $product->filament->is_active)
        || ($hasManagedColors && ! collect($inventoryColors)->contains(fn ($color) => $color['in_stock']));
      $firstColor = collect($productColors)->first(fn ($color) => $color['in_stock'] && ! $isOutOfStock)['name'] ?? null;
      $gallery = $product->gallery();
    @endphp
    <title>{{ $product->name }} — TRIO 3D Printing</title>
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Alexandria:wght@400;500;600;700;800&family=DM+Mono:wght@400;500&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="@assetv('trio.css')" />
    <link rel="stylesheet" href="@assetv('store-overrides.css')" />
    <style>
      .product-detail-wrap { max-width: 1280px; margin: 40px auto 100px; padding: 0 42px; }
      .breadcrumb { font-family: var(--mono); font-size: 11px; color: var(--muted); margin-bottom: 30px; display: flex; gap: 8px; }
      .breadcrumb a { color: var(--muted); text-decoration: none; }
      .breadcrumb a:hover { color: var(--lime); }
      .product-detail-grid { display: grid; grid-template-columns: 1fr 1.15fr; gap: 60px; align-items: start; }
      .product-hero-visual { position: relative; height: 520px; border-radius: 8px; overflow: hidden; background: linear-gradient(145deg, #101e29, #08121a); border: 1px solid var(--line); display: grid; place-items: center; }
      .product-hero-visual img { width: 100%; height: 100%; padding: 16px; object-fit: contain; }
      .product-gallery-thumbs { display: flex; flex-wrap: wrap; gap: 10px; margin-top: 12px; }
      .gallery-thumb-btn { width: 72px; height: 72px; padding: 0; border-radius: 6px; overflow: hidden; border: 1px solid var(--line); background: #0c1822; cursor: pointer; transition: border-color .2s, transform .2s; }
      .gallery-thumb-btn img { width: 100%; height: 100%; object-fit: cover; display: block; }
      .gallery-thumb-btn:hover { transform: translateY(-2px); }
      .gallery-thumb-btn.active { border-color: var(--lime); box-shadow: 0 0 12px rgba(200,242,40,.25); }
      .spec-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin: 25px 0; }
      .spec-card { background: #0c1822; border: 1px solid var(--line); padding: 14px; border-radius: 6px; }
      .spec-card span { display: block; font-size: 10px; font-family: var(--mono); color: var(--muted); margin-bottom: 5px; }
      .spec-card strong { font-size: 14px; color: #fff; }
      .qty-selector { display: flex; align-items: center; gap: 12px; margin: 25px 0 30px; }
      .qty-btn { width: 36px; height: 36px; background: #0e1c26; border: 1px solid var(--line); color: #fff; border-radius: 4px; font-size: 18px; cursor: pointer; display: grid; place-items: center; }
      .qty-num { font-family: var(--mono); font-size: 16px; min-width: 30px; text-align: center; }
      @media(max-width: 900px) {
        .product-detail-grid { grid-template-columns: 1fr; gap: 35px; }
        .product-hero-visual { height: 380px; }
      }
    </style>
  </head>
  <body>
    <div class="noise"></div>
    <div class="glow glow-a"></div><div class="glow glow-b"></div>

    @include('partials.store-header', ['activeNav' => 'catalog'])

    <main class="product-detail-wrap">
      <div class="breadcrumb">
        <a href="{{ route('storefront') }}">الرئيسية</a> <span>/</span>
        <a href="{{ route('catalog.index') }}">المنتجات</a> <span>/</span>
        <span style="color:var(--lime);">{{ $product->name }}</span>
      </div>

      <div class="product-detail-grid">
        <!-- المعرض البصري للمنتج -->
        <div>
          <div class="product-hero-visual">
            @if($product->coverImage())
              <img id="product-main-image" src="@sasset($product->coverImage())" alt="{{ $product->name }}" />
            @else
              <div class="product-visual stand"><span></span><i></i><b></b></div>
            @endif
          </div>

          @if(count($gallery) > 1)
            <div class="product-gallery-thumbs">
              @foreach($gallery as $index => $path)
                <button type="button"
                        class="gallery-thumb-btn {{ $index === 0 ? 'active' : '' }}"
                        data-full="@sasset($path)"
                        aria-label="عرض الصورة {{ $index + 1 }}">
                  <img src="@sasset($path)" alt="{{ $product->name }} - صورة {{ $index + 1 }}" />
                </button>
              @endforeach
            </div>
          @endif
        </div>

        <!-- معلومات وتفاصيل المنتج والطلب -->
        <div>
          <span class="kicker" style="font-size:11px;">TRIO HARDWARE · {{ $product->filament?->name ?? 'PLA+' }}</span>
          <h1 style="font-size: clamp(34px, 3.5vw, 48px); margin: 8px 0 12px 0;">{{ $product->name }}</h1>

          <div style="display:flex; align-items:baseline; gap:12px; margin-bottom: 20px;">
            <strong style="font-family: var(--mono); font-size: 28px; color: var(--lime);">
              EGP {{ number_format((float) $product->price, 2) }}
            </strong>
            <small style="color: var(--muted); font-size:12px;">شامل التصنيع والتجهيز</small>
          </div>

          <p style="font-size: 15px; line-height: 1.8; color: #b5c4c9; margin: 0 0 20px 0;">
            {{ $product->description ?: 'قطعة أصلية مصممة هندسياً ومطبوعة بالكامل بتقنية الـ 3D Printing لتوفير أقصى درجات التحمل والشكل العصري.' }}
          </p>

          <!-- جدول المواصفات الفنية -->
          <div class="spec-grid">
            <div class="spec-card">
              <span>وزن القطعة</span>
              <strong>{{ $product->weight_grams }} جرام</strong>
            </div>

            <div class="spec-card">
              <span>الأبعاد (طول × عرض × ارتفاع)</span>
              <strong>{{ $product->dimensions ?: '110 × 80 × 95 mm' }}</strong>
            </div>

            <div class="spec-card">
              <span>نوع خامة الفيلمنت</span>
              <strong style="color:var(--lime);">{{ $product->filament?->name ?? 'PLA+ متين' }}</strong>
            </div>

            <div class="spec-card">
              <span>دقة طباعة الطبقات</span>
              <strong>0.16 mm Precision</strong>
            </div>
          </div>

          @if($product->details)
            <div style="background:#09151e; border:1px solid var(--line); border-radius:6px; padding:15px; margin-bottom:20px;">
              <span style="font-size:11px; font-family:var(--mono); color:var(--lime); display:block; margin-bottom:6px;">مواصفات إضافية وتفاصيل الاستخدام:</span>
              <p style="margin:0; font-size:13px; line-height:1.7; color:#c5d4d8;">{{ $product->details }}</p>
            </div>
          @endif

          @php
            if (!function_exists('getVariantSwatchColor')) {
              function getVariantSwatchColor($name) {
                $n = mb_strtolower($name);
                if (str_contains($n, 'برتقال') || str_contains($n, 'orange')) return '#ff7a18';
                if (str_contains($n, 'أسود') || str_contains($n, 'اسود') || str_contains($n, 'black')) return '#15191e';
                if (str_contains($n, 'ليمون') || str_contains($n, 'lime') || str_contains($n, 'أخضر') || str_contains($n, 'اخضر') || str_contains($n, 'green')) return '#4ade80';
                if (str_contains($n, 'أبيض') || str_contains($n, 'ابيض') || str_contains($n, 'white')) return '#f8fafc';
                if (str_contains($n, 'رماد') || str_contains($n, 'grey') || str_contains($n, 'gray')) return '#64748b';
                if (str_contains($n, 'أحمر') || str_contains($n, 'احمر') || str_contains($n, 'red')) return '#ef4444';
                if (str_contains($n, 'أزرق') || str_contains($n, 'ازرق') || str_contains($n, 'blue')) return '#3b82f6';
                if (str_contains($n, 'أصفر') || str_contains($n, 'اصفر') || str_contains($n, 'yellow')) return '#eab308';
                if (str_contains($n, 'بنفسج') || str_contains($n, 'purple')) return '#a855f7';
                return '#38bdf8';
              }
            }
          @endphp

          <!-- خيارات الألوان والتخصيص للمنتج -->
          @if($productColors !== [])
            <div class="product-color-options" style="margin-bottom: 24px; padding: 18px; background: rgba(9, 21, 30, 0.7); border: 1px solid var(--line); border-radius: 8px;">
              <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                <span style="font-size: 13px; font-weight: 600; color: #fff; display: flex; align-items: center; gap: 8px;">
                  <span style="width: 8px; height: 8px; border-radius: 50%; background: var(--lime); display: inline-block; box-shadow: 0 0 8px var(--lime);"></span>
                  {{ $product->color_label ?: 'الألوان والتخصيص المتاح' }}:
                </span>
                <span id="active-color-name" style="font-family: var(--mono); font-size: 12px; color: var(--lime); font-weight: bold; background: rgba(74,222,128,0.1); padding: 3px 8px; border-radius: 4px; border: 1px solid rgba(74,222,128,0.25);">
                  {{ $firstColor }}
                </span>
              </div>
              <div class="color-pills-wrap" style="display: flex; flex-wrap: wrap; gap: 10px;">
                @foreach($productColors as $idx => $color)
                  @php
                    $dotColor = getVariantSwatchColor($color['name']);
                    $colorInStock = $color['in_stock'] && ! $isOutOfStock;
                    $colorIsSelected = $colorInStock && $color['name'] === $firstColor;
                  @endphp
                  <button type="button"
                          class="color-pill-btn {{ $colorIsSelected ? 'active' : '' }}"
                          data-color="{{ $color['name'] }}"
                          @disabled(! $colorInStock)
                          style="padding: 9px 14px; border-radius: 6px; font-size: 13px; font-weight: 500; cursor: {{ $colorInStock ? 'pointer' : 'not-allowed' }}; transition: all 0.2s ease; border: 1px solid {{ $colorIsSelected ? 'var(--lime)' : 'rgba(255,255,255,0.14)' }}; background: {{ $colorIsSelected ? 'rgba(74, 222, 128, 0.14)' : '#0d1821' }}; color: {{ $colorIsSelected ? '#fff' : '#c5d4d8' }}; opacity: {{ $colorInStock ? '1' : '0.5' }}; display: flex; align-items: center; gap: 8px; box-shadow: {{ $colorIsSelected ? '0 0 10px rgba(74, 222, 128, 0.2)' : 'none' }};">
                    <span class="color-dot" style="width: 12px; height: 12px; border-radius: 50%; background: {{ $dotColor }}; border: 1px solid rgba(255,255,255,0.4); display: inline-block; flex-shrink: 0;"></span>
                    <span>{{ $color['name'] }}</span>
                    @if(! $colorInStock)
                      <small>نفد</small>
                    @endif
                    <span class="check-mark" style="font-size: 11px; color: var(--lime); display: {{ $colorIsSelected ? 'inline' : 'none' }};">✓</span>
                  </button>
                @endforeach
              </div>
              <p style="margin: 10px 0 0 0; font-size: 11px; color: var(--muted); line-height: 1.6;">
                💡 الألوان المتاحة حاليًا ظاهرة هنا، واللون المنفد لا يمكن طلبه.
              </p>
            </div>
          @elseif($hasManagedColors)
            <p class="checkout-note" style="color:#ff8e7a;">نفدت جميع ألوان هذه الخامة حاليًا.</p>
          @endif

          <!-- اختيار الكمية والإضافة للسلة -->
          <div class="qty-selector">
            <span style="font-size:12px; color:var(--muted);">الكمية:</span>
            <button class="qty-btn" id="qty-minus" type="button">-</button>
            <span class="qty-num" id="qty-value">1</span>
            <button class="qty-btn" id="qty-plus" type="button">+</button>
          </div>

          <div style="display:flex; gap:14px; flex-wrap:wrap;">
                <button class="button button-primary" id="detail-add-btn" style="flex:1; justify-content:center; padding:16px;"
                  data-id="{{ $product->id }}" data-name="{{ $product->name }}" data-price="{{ $product->price }}" data-image="@if($product->coverImage())@sasset($product->coverImage())@endif" @disabled($isOutOfStock)>
              {{ $isOutOfStock ? 'نفد المخزون' : 'أضف لسلة الطلبات' }} <span>+</span>
            </button>

            @php
              $initialMsg = "أهلاً استوديو TRIO، حابب أطلب أو أستفسر عن منتج: {$product->name}";
              if ($firstColor) {
                $initialMsg .= " (اللون: {$firstColor})";
              }
              $initialMsg .= " بسعر EGP ".number_format($product->price, 2);
              $waMsg = urlencode($initialMsg);
            @endphp
            <a href="https://wa.me/{{ config('app.whatsapp_phone', '201282537892') }}?text={{ $waMsg }}" id="quick-wa-btn" target="_blank" rel="noopener noreferrer" class="button button-quiet" style="border-color:#25d366; color:#25d366; justify-content:center; padding:16px;">
              استفسار عبر واتساب 💬
            </a>
          </div>
        </div>
      </div>

      <!-- منتجات أخرى قد تعجبك -->
      @if($relatedProducts->isNotEmpty())
        <section style="margin-top: 100px; padding-top: 50px; border-top: 1px solid var(--line);">
          <div class="section-heading">
            <div>
              <span class="kicker">EXPLORE MORE</span>
              <h2>منتجات أخرى قد تعجبك</h2>
            </div>
            <a href="{{ route('catalog.index') }}" class="text-link">شاهد الكتالوج بالكامل <span>↙</span></a>
          </div>

          <div class="product-grid related-products" aria-label="منتجات أخرى قد تعجبك" tabindex="0">
            @foreach($relatedProducts as $rel)
              @php
                $relatedInventory = $rel->filament?->colorInventory() ?? [];
                $relatedHasManagedInventory = $rel->filament?->hasManagedColorInventory() ?? false;
                $relatedIsOutOfStock = ($rel->filament && ! $rel->filament->is_active)
                  || ($relatedHasManagedInventory && ! collect($relatedInventory)->contains(fn ($color) => $color['in_stock']));
              @endphp
              <article class="product-card product-lime" style="height:380px;">
                <a href="{{ route('catalog.show', $rel) }}" style="position:absolute; inset:0; z-index:1;"></a>
                @if($rel->image_path)
                  <img class="product-uploaded-image" src="@sasset($rel->image_path)" alt="{{ $rel->name }}" />
                @else
                  <div class="product-visual stand"><span></span><i></i><b></b></div>
                @endif
                <div class="product-info" style="position:relative; z-index:2; pointer-events:none;">
                  <div><h3>{{ $rel->name }}</h3><p>{{ $rel->weight_grams }}g</p></div>
                  <strong>EGP {{ number_format((float) $rel->price, 2) }}</strong>
                </div>
                @if($relatedIsOutOfStock)
                  <button class="add-button" style="z-index:3;" disabled>نفد المخزون</button>
                @elseif($relatedHasManagedInventory)
                  <a class="add-button" style="z-index:3;" href="{{ route('catalog.show', $rel) }}">اختار اللون <span>↗</span></a>
                @else
                  <button class="add-button" style="z-index:3;" data-id="{{ $rel->id }}" data-name="{{ $rel->name }}" data-price="{{ $rel->price }}">أضف للسلة <span>+</span></button>
                @endif
              </article>
            @endforeach
          </div>
        </section>
      @endif
    </main>

    @include('partials.store-footer')

    <!-- سلة الطلبات -->
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
    <script>
      // إدارة اختيار ألوان وتخصيص المنتج
      let selectedColor = @json($firstColor);
      const colorPills = document.querySelectorAll('.color-pill-btn');
      const activeColorNameEl = document.getElementById('active-color-name');
      const quickWaBtn = document.getElementById('quick-wa-btn');
      const productName = @json($product->name);
      const productPrice = '{{ number_format($product->price, 2) }}';
      const waPhone = window.TRIO_CONFIG.whatsappPhone || '201282537892';

      function updateWaLink() {
        if (!quickWaBtn) return;
        let text = `أهلاً استوديو TRIO، حابب أطلب أو أستفسر عن منتج: ${productName}`;
        if (selectedColor) {
          text += ` (اللون / التخصيص: ${selectedColor})`;
        }
        text += ` بسعر EGP ${productPrice}`;
        quickWaBtn.href = `https://wa.me/${waPhone}?text=${encodeURIComponent(text)}`;
      }

      if (colorPills.length > 0) {
        colorPills.forEach((btn) => {
          btn.addEventListener('click', () => {
            colorPills.forEach((p) => {
              p.classList.remove('active');
              p.style.borderColor = 'rgba(255,255,255,0.14)';
              p.style.background = '#0d1821';
              p.style.color = '#c5d4d8';
              p.style.boxShadow = 'none';
              const chk = p.querySelector('.check-mark');
              if (chk) chk.style.display = 'none';
            });

            btn.classList.add('active');
            btn.style.borderColor = 'var(--lime)';
            btn.style.background = 'rgba(74, 222, 128, 0.14)';
            btn.style.color = '#fff';
            btn.style.boxShadow = '0 0 10px rgba(74, 222, 128, 0.2)';
            const chk = btn.querySelector('.check-mark');
            if (chk) chk.style.display = 'inline';

            selectedColor = btn.dataset.color;
            if (activeColorNameEl) activeColorNameEl.textContent = selectedColor;
            updateWaLink();
          });
        });
      }

      // تفاعل محدد الكمية والإضافة للسلة
      let qty = 1;
      const qtyVal = document.getElementById('qty-value');
      const addBtn = document.getElementById('detail-add-btn');
      document.getElementById('qty-plus').addEventListener('click', () => { qty++; qtyVal.textContent = qty; });
      document.getElementById('qty-minus').addEventListener('click', () => { if(qty > 1) qty--; qtyVal.textContent = qty; });
      addBtn.addEventListener('click', () => {
        if (addBtn.disabled) return;
        for(let i=0; i<qty; i++) {
          addItem(addBtn.dataset.id, addBtn.dataset.name, addBtn.dataset.price, selectedColor, addBtn.dataset.image);
        }
      });
    </script>
  </body>
</html>
