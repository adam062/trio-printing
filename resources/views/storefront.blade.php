<!doctype html>
<html lang="ar" dir="rtl">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="theme-color" content="#071019" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>TRIO — استوديو ومنتجات الطباعة ثلاثية الأبعاد</title>
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Alexandria:wght@400;500;600;700;800&family=DM+Mono:wght@400;500&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('trio.css') }}" />
    <link rel="stylesheet" href="{{ asset('store-overrides.css') }}" />
  </head>
  <body>
    <div class="noise"></div>
    <div class="glow glow-a"></div><div class="glow glow-b"></div>

    <header class="site-header">
      <a class="brand" href="{{ route('storefront') }}" aria-label="TRIO homepage">
        <span class="brand-mark"><i></i><i></i><i></i></span><span>TRIO</span>
      </a>
      <nav class="nav-links" aria-label="التنقل الرئيسي">
        <a href="#top">الرئيسية</a>
        <a href="{{ route('catalog.index') }}" style="color:var(--lime);">المتجر والكتالوج</a>
        <a href="#custom">اطبع فكرتك</a>
        <a href="#about">فكرة TRIO</a>
      </nav>
      <div class="header-actions">
        <button class="icon-button theme-button" aria-label="تبديل المظهر"><span>☼</span></button>
        <button class="cart-button" id="open-cart"><span class="cart-count" id="cart-count">0</span><span>سلة الطلبات</span><span class="arrow">↙</span></button>
      </div>
    </header>

    <main id="top">
      <section class="hero">
        <div class="hero-copy reveal">
          <div class="eyebrow"><span class="pulse-dot"></span> ثلاث أفكار. طبقة واحدة في كل مرة.</div>
          <h1>نحوّل أفكارك<br/><em>لأشياء تمسكها.</em></h1>
          <p>TRIO هو استوديو طباعة ثلاثية الأبعاد بنصمّم، نطبع، وننجز القطع التي كانت لسه مجرد فكرة عندك.</p>
          <div class="hero-actions">
            <a class="button button-primary" href="{{ route('catalog.index') }}">تصفح الكتالوج الكامل <span>↙</span></a>
            <a class="button button-quiet" href="#custom">ارفع تصميمك <span>↑</span></a>
          </div>
          <div class="hero-meta"><span><b>01</b> تصميم</span><span><b>02</b> طباعة</span><span><b>03</b> توصيل</span></div>
        </div>
        <div class="hero-art reveal-delay">
          <div class="trio-printer-chamber">
            <svg class="printer-svg" viewBox="0 0 480 440" fill="none" xmlns="http://www.w3.org/2000/svg">
              <defs>
                <!-- خامات وتدرجات الإضاءة والألمونيوم -->
                <radialGradient id="chamberBack" cx="50%" cy="20%" r="80%">
                  <stop offset="0%" stop-color="#142634" />
                  <stop offset="60%" stop-color="#08131b" />
                  <stop offset="100%" stop-color="#04090e" />
                </radialGradient>

                <linearGradient id="chassisGrad" x1="0" y1="0" x2="1" y2="0">
                  <stop offset="0%" stop-color="#1e323e" />
                  <stop offset="30%" stop-color="#2d4857" />
                  <stop offset="70%" stop-color="#172731" />
                  <stop offset="100%" stop-color="#0d1820" />
                </linearGradient>

                <linearGradient id="metalGrad" x1="0" y1="0" x2="1" y2="0">
                  <stop offset="0%" stop-color="#3a515f" />
                  <stop offset="50%" stop-color="#557385" />
                  <stop offset="100%" stop-color="#2a3d49" />
                </linearGradient>

                <linearGradient id="leadScrew" x1="0" y1="0" x2="1" y2="0">
                  <stop offset="0%" stop-color="#415764" />
                  <stop offset="40%" stop-color="#93b0bf" />
                  <stop offset="60%" stop-color="#e2edf2" />
                  <stop offset="100%" stop-color="#2f424c" />
                </linearGradient>

                <linearGradient id="carbonGantry" x1="0" y1="0" x2="0" y2="1">
                  <stop offset="0%" stop-color="#161f24" />
                  <stop offset="50%" stop-color="#2b3b44" />
                  <stop offset="100%" stop-color="#0e1418" />
                </linearGradient>

                <linearGradient id="bedPlate" x1="0" y1="0" x2="0" y2="1">
                  <stop offset="0%" stop-color="#1c2c36" />
                  <stop offset="100%" stop-color="#0f1920" />
                </linearGradient>

                <linearGradient id="nozzleGrad" x1="0" y1="0" x2="1" y2="0">
                  <stop offset="0%" stop-color="#b8860b" />
                  <stop offset="50%" stop-color="#ffd700" />
                  <stop offset="100%" stop-color="#8b6508" />
                </linearGradient>

                <filter id="neonGlow" x="-30%" y="-30%" width="160%" height="160%">
                  <feGaussianBlur stdDeviation="5" result="blur" />
                  <feMerge>
                    <feMergeNode in="blur" />
                    <feMergeNode in="SourceGraphic" />
                  </feMerge>
                </filter>

                <filter id="laserSparkGlow" x="-50%" y="-50%" width="200%" height="200%">
                  <feGaussianBlur stdDeviation="4" result="blur1" />
                  <feGaussianBlur stdDeviation="8" result="blur2" />
                  <feMerge>
                    <feMergeNode in="blur2" />
                    <feMergeNode in="blur1" />
                    <feMergeNode in="SourceGraphic" />
                  </feMerge>
                </filter>

                <!-- شبكة تقنية لخلفية الغرفة -->
                <pattern id="gridPattern" width="20" height="20" patternUnits="userSpaceOnUse">
                  <path d="M 20 0 L 0 0 0 20" fill="none" stroke="rgba(255,255,255,0.03)" stroke-width="1" />
                </pattern>
              </defs>

              <!-- خلفية الغرفة والشبكة -->
              <rect x="0" y="0" width="480" height="440" fill="url(#chamberBack)" />
              <rect x="0" y="0" width="480" height="440" fill="url(#gridPattern)" />

              <!-- إضاءة شريط LED العلوي -->
              <rect x="50" y="14" width="380" height="6" rx="3" fill="#ffffff" filter="url(#neonGlow)" opacity="0.8" />
              <rect x="60" y="15" width="360" height="4" rx="2" fill="#c8f228" opacity="0.6" />

              <!-- هيكل الطابعة الخارجي: العواميد والأركان -->
              <!-- العواميد الخلفية -->
              <rect x="52" y="24" width="12" height="370" rx="2" fill="url(#metalGrad)" opacity="0.45" />
              <rect x="416" y="24" width="12" height="370" rx="2" fill="url(#metalGrad)" opacity="0.45" />

              <!-- مسامير وقضبان المحور الرأسي Z Dual Lead Screws -->
              <rect x="74" y="32" width="6" height="350" rx="1" fill="url(#leadScrew)" opacity="0.8" />
              <line x1="74" y1="32" x2="80" y2="382" stroke="#1d2e37" stroke-width="1.5" stroke-dasharray="3 3" opacity="0.7" />
              <rect x="400" y="32" width="6" height="350" rx="1" fill="url(#leadScrew)" opacity="0.8" />
              <line x1="400" y1="32" x2="406" y2="382" stroke="#1d2e37" stroke-width="1.5" stroke-dasharray="3 3" opacity="0.7" />

              <!-- العواميد الأمامية القوية 4040 Extrusions -->
              <rect x="18" y="16" width="20" height="400" rx="3" fill="url(#chassisGrad)" />
              <rect x="23" y="18" width="4" height="396" fill="rgba(255,255,255,0.12)" />
              <rect x="442" y="16" width="20" height="400" rx="3" fill="url(#chassisGrad)" />
              <rect x="445" y="18" width="4" height="396" fill="rgba(255,255,255,0.12)" />

              <!-- جسور الإطار العلوي والسفلي -->
              <rect x="18" y="16" width="444" height="18" rx="2" fill="url(#chassisGrad)" />
              <rect x="18" y="398" width="444" height="20" rx="2" fill="url(#chassisGrad)" />

              <!-- بكرات سيور CoreXY العلوية -->
              <circle cx="58" cy="46" r="11" fill="#1b2a33" stroke="var(--lime)" stroke-width="1.5" opacity="0.85" />
              <circle cx="58" cy="46" r="4" fill="#c8f228" />
              <circle cx="422" cy="46" r="11" fill="#1b2a33" stroke="var(--lime)" stroke-width="1.5" opacity="0.85" />
              <circle cx="422" cy="46" r="4" fill="#c8f228" />

              <!-- سيور التايمنج CoreXY المطاطية -->
              <line x1="68" y1="46" x2="412" y2="46" stroke="#0e171d" stroke-width="4" stroke-dasharray="2 1" />
              <line x1="68" y1="46" x2="412" y2="46" stroke="#2a424f" stroke-width="1" />

              <!-- سرير الطباعة الحراري Heated Bed في منظور أيزومتري -->
              <!-- ذراعي رفع السرير Z -->
              <rect x="70" y="300" width="340" height="12" rx="3" fill="#12202a" stroke="#253a47" stroke-width="1" />

              <!-- صفيحة السرير PEI Spring Steel Plate -->
              <polygon points="76,312 404,312 376,368 104,368" fill="url(#bedPlate)" stroke="rgba(200,242,40,0.3)" stroke-width="1.5" />
              <polygon points="80,314 400,314 373,366 107,366" fill="#111c23" />

              <!-- شبكة السرير الهندسية المطبوعة (10mm PEI Grid Lines) -->
              <line x1="140" y1="314" x2="160" y2="366" stroke="rgba(200,242,40,0.15)" stroke-width="1" />
              <line x1="200" y1="314" x2="215" y2="366" stroke="rgba(200,242,40,0.15)" stroke-width="1" />
              <line x1="260" y1="314" x2="270" y2="366" stroke="rgba(200,242,40,0.15)" stroke-width="1" />
              <line x1="320" y1="314" x2="325" y2="366" stroke="rgba(200,242,40,0.15)" stroke-width="1" />
              <line x1="88" y1="330" x2="392" y2="330" stroke="rgba(200,242,40,0.15)" stroke-width="1" />
              <line x1="97" y1="348" x2="383" y2="348" stroke="rgba(200,242,40,0.15)" stroke-width="1" />

              <!-- شريط المسح والتنظيف Purge Line على طرف السرير -->
              <line x1="92" y1="320" x2="114" y2="362" stroke="#c8f228" stroke-width="3" stroke-linecap="round" opacity="0.9" filter="url(#neonGlow)" />

              <!-- المجسم المطبوع 3D PRINTED OBJECT جاري بناؤه على السرير -->
              <g class="printed-model" transform="translate(0, 0)">
                <!-- القاعدة السفلية للمجسم (Layers 01 - 100) -->
                <polygon points="170,305 310,305 328,328 152,328" fill="#152530" stroke="#243b49" stroke-width="1" />
                <line x1="168" y1="310" x2="312" y2="310" stroke="#1d3444" stroke-width="1" />
                <line x1="164" y1="316" x2="316" y2="316" stroke="#1d3444" stroke-width="1" />
                <line x1="158" y1="322" x2="322" y2="322" stroke="#1d3444" stroke-width="1" />

                <!-- جسم القطعة الأوسط الهندسي المتدرج (Layers 101 - 180) -->
                <polygon points="185,260 295,260 310,305 170,305" fill="#1a3240" stroke="#2d4f64" stroke-width="1.2" />
                <polygon points="215,260 265,260 278,305 202,305" fill="#203e50" stroke="#355c74" stroke-width="1" />

                <!-- خطوط طبقات ميكرونية 0.16mm Layer lines -->
                <line x1="182" y1="270" x2="298" y2="270" stroke="rgba(200,242,40,0.2)" stroke-width="0.8" />
                <line x1="179" y1="280" x2="301" y2="280" stroke="rgba(200,242,40,0.2)" stroke-width="0.8" />
                <line x1="175" y1="292" x2="305" y2="292" stroke="rgba(200,242,40,0.2)" stroke-width="0.8" />

                <!-- الطبقة النشطة الحالية (Active Top Layer - Layer 184) بتوهج نيون ليموني مشع -->
                <polygon points="185,260 295,260 288,272 192,272" fill="rgba(200,242,40,0.18)" stroke="#c8f228" stroke-width="1.8" filter="url(#neonGlow)" />
                <!-- نمط الحشو الداخلي النشط Infill Lattice -->
                <path d="M 195 264 L 285 264 M 205 268 L 275 268 M 215 260 L 225 272 M 240 260 L 250 272 M 265 260 L 275 272" 
                      stroke="#c8f228" stroke-width="1.2" opacity="0.85" />
              </g>

              <!-- جسر المحور الأفقي المتحرك X-Gantry (يتحرك رأسياً حسب Z) -->
              <g class="anim-gantry">
                <!-- قضيبين من ألياف الكربون Dual Carbon Rods -->
                <rect x="42" y="80" width="396" height="7" rx="3" fill="url(#carbonGantry)" stroke="#3a4f5b" stroke-width="0.8" />
                <rect x="42" y="94" width="396" height="7" rx="3" fill="url(#carbonGantry)" stroke="#3a4f5b" stroke-width="0.8" />
              </g>

              <!-- رأس الطباعة الذكي عالي السرعة (Toolhead / Extruder) يتحرك بسلاسة أثناء الطباعة -->
              <g class="anim-toolhead">
                <!-- أنبوب تغذية الفيلمنت PTFE Bowden Tube مرن يمتد من الأعلى -->
                <path d="M 0 -80 C 15 -40, 20 -15, 0 0" stroke="#38bdf8" stroke-width="2.5" fill="none" opacity="0.8" stroke-linecap="round" />
                <path d="M -5 -75 C 5 -35, 12 -12, -4 0" stroke="#c8f228" stroke-width="1.2" fill="none" opacity="0.9" />

                <!-- جسم رأس الطباعة الرئيسي (Stealth Enclosure) -->
                <path d="M -26 -12 L 26 -12 L 32 30 L 18 52 L -18 52 L -32 30 Z" fill="#14212a" stroke="#365060" stroke-width="1.5" />
                <!-- تفاصيل الواجهة وغطاء التبريد -->
                <rect x="-18" y="-4" width="36" height="24" rx="2" fill="#0d171e" stroke="#253844" stroke-width="1" />

                <!-- مروحة التبريد التوربينية الدائرية (Radial Fan Intake) -->
                <circle cx="0" cy="8" r="9" fill="#172731" stroke="#3a5363" stroke-width="1" />
                <circle cx="0" cy="8" r="4" fill="#0b1318" />
                <line x1="-7" y1="8" x2="7" y2="8" stroke="#48687a" stroke-width="1" />
                <line x1="0" y1="1" x2="0" y2="15" stroke="#48687a" stroke-width="1" />

                <!-- شريط حالة RGB ذكي ينبض باللون الليموني -->
                <rect x="-14" y="36" width="28" height="3" rx="1.5" fill="#c8f228" filter="url(#neonGlow)" />

                <!-- كتلة التسخين Hotend Heater Block -->
                <rect x="-8" y="52" width="16" height="10" rx="1" fill="#465b66" stroke="#26343b" stroke-width="1" />

                <!-- فوهة الطباعة النحاسية الدقيقة (Brass Nozzle 0.4mm) -->
                <polygon points="-6,62 6,62 2,72 -2,72" fill="url(#nozzleGrad)" />

                <!-- نقطة صهر الفيلمنت وترسيب البلاستيك النشط (Extrusion Plasma Spark) -->
                <circle class="anim-spark" cx="0" cy="74" r="3.5" fill="#c8f228" filter="url(#laserSparkGlow)" />
                <line x1="0" y1="74" x2="0" y2="82" stroke="#c8f228" stroke-width="2" stroke-linecap="round" opacity="0.9" filter="url(#neonGlow)" />
              </g>
            </svg>

            <!-- شاشات وبيانات التشخيص التفاعلية (Floating Industrial HUD) -->
            <div class="printer-hud">
              <!-- بطاقة الحالة العلوية -->
              <div class="hud-pill hud-pill-top">
                <span class="hud-live-dot"></span>
                <span>CORE-XY · 320 mm/s</span>
              </div>

              <!-- بطاقة الدقة والاستوديو -->
              <div class="hud-pill hud-pill-spec">
                <span>TRIO LABS ✦ BATCH #08</span>
              </div>

              <!-- بطاقة درجات الحرارة الحية -->
              <div class="hud-pill hud-pill-thermals">
                <div>
                  <span style="color:#f87171;">♨</span>
                  <span>NOZZLE: <strong>220°C</strong></span>
                </div>
                <div>
                  <span style="color:#38bdf8;">⚡</span>
                  <span>BED: <strong>60°C</strong></span>
                </div>
              </div>

              <!-- بطاقة شريط تقدم الطباعة والطبقات -->
              <div class="hud-pill hud-pill-progress">
                <span>LAYER <strong>184 / 350</strong> · 0.16mm</span>
                <div style="display:flex; align-items:center; gap:8px;">
                  <div class="hud-progress-bar"><div class="hud-progress-fill"></div></div>
                  <strong style="color:var(--lime);">58%</strong>
                </div>
              </div>
            </div>
          </div>

          <!-- إحصائية الدقة بجوار الطابعة -->
          <div class="hero-stat"><b>0.16</b><span>mm<br/>precision</span></div>
        </div>
        <div class="scroll-cue"><span></span> اكتشف TRIO</div>
      </section>

      <section class="ticker" aria-label="مزايا الخدمة"><div class="ticker-track"><span>CREATED BY THREE</span><b>✦</b><span>MADE FOR YOUR DESK</span><b>✦</b><span>PRINTED LOCALLY</span><b>✦</b><span>DESIGNED TO LAST</span><b>✦</b><span>CREATED BY THREE</span><b>✦</b><span>MADE FOR YOUR DESK</span><b>✦</b></div></section>

      <!-- قسم المنتجات المميزة كسلايدر أفقي سلس مع أسهم -->
      <section class="shop section" id="shop">
        <div class="section-heading reveal">
          <div>
            <span class="kicker">01 / FEATURED DROPS</span>
            <h2>قطع مختارة.<br/><em>فرق كبير.</em></h2>
          </div>
          <div style="display:flex; gap:14px; align-items:center;">
            <!-- أسهم التنقل بالسلايدر -->
            <div style="display:flex; gap:8px;">
              <button type="button" id="carousel-prev" class="carousel-arrow" title="السابق">→</button>
              <button type="button" id="carousel-next" class="carousel-arrow" title="التالي">←</button>
            </div>
            <a href="{{ route('catalog.index') }}" class="button button-quiet" style="padding:10px 18px; font-size:11px;">
              عرض كل المنتجات ({{ $products->count() }}) ↗
            </a>
          </div>
        </div>

        <div style="position:relative; overflow:hidden;">
          <div class="product-carousel-track" id="product-carousel" style="display:flex; gap:20px; overflow-x:auto; scroll-behavior:smooth; scroll-snap-type:x mandatory; padding:15px 4px 25px 4px;">
            @php($visuals = ['stand', 'headphone', 'planters'])
            @forelse($products as $product)
              <article class="product-card product-{{ ['lime', 'blue', 'dark'][$loop->index % 3] }}" 
                       style="flex: 0 0 min(330px, 85vw); height:450px; scroll-snap-align: start;">
                <div class="product-tag">{{ $product->filament?->name ?? 'TRIO PRODUCT' }}</div>

                <!-- رابط مباشر لصفحة تفاصيل المنتج المنفصلة -->
                <a href="{{ route('catalog.show', $product) }}" style="position:absolute; inset:0; z-index:1;" aria-label="عرض تفاصيل {{ $product->name }}"></a>

                @if($product->image_path)
                  <img class="product-uploaded-image" src="{{ asset('storage/'.$product->image_path) }}" alt="{{ $product->name }}" />
                @else
                  <div class="product-visual {{ $visuals[$loop->index % 3] }}"><span></span><i></i><b></b></div>
                @endif

                <div class="product-info" style="position:relative; z-index:2; pointer-events:none;">
                  <div>
                    <h3 style="margin-bottom:4px;">{{ $product->name }}</h3>
                    <p style="margin:0; opacity:0.8;">
                      {{ $product->weight_grams }}g 
                      @if($product->dimensions) · {{ $product->dimensions }} @endif
                    </p>
                  </div>
                  <strong style="font-size:14px;">EGP {{ number_format((float) $product->price, 2) }}</strong>
                </div>

                <button class="add-button" style="z-index:3;" data-id="{{ $product->id }}" data-name="{{ $product->name }}" data-price="{{ $product->price }}">
                  أضف للسلة <span>+</span>
                </button>
              </article>
            @empty
              <div class="empty-products">قريبًا: أول منتجات TRIO.</div>
            @endforelse
          </div>
        </div>
      </section>

      <!-- قسم اطبع فكرتك -->
      <section class="custom section" id="custom">
        <div class="custom-grid">
          <div class="custom-copy reveal"><span class="kicker">02 / YOUR FILE, OUR CRAFT</span><h2>عندك ملف 3D؟<br/><em>خلّيه حقيقة.</em></h2><p>ارفع ملف الـ STL أو 3MF، اختر نوع الخامة واللون، واحصل على تقدير فوري للسعر. نراجع كل ملف هندسيًا قبل الطباعة.</p>
            <ul class="feature-list">
              <li><span>✓</span> دعم ملفات STL و 3MF حتى 100MB</li>
              <li><span>✓</span> خامات متعددة: PLA+, PETG, TPU</li>
              <li><span>✓</span> مراجعة بشرية ودعم عبر واتساب</li>
            </ul>
          </div>
          <form class="quote-card reveal-delay" id="quote-form">
            <div class="quote-top"><span>INSTANT ESTIMATE</span><i id="quote-status">جاهز للتسعير</i></div>
            <label class="dropzone" id="dropzone" for="file-input">
              <input id="file-input" type="file" accept=".stl,.3mf,.zip" hidden />
              <span class="upload-icon">↑</span>
              <b id="file-label">اسحب ملفك هنا أو اضغط للاختيار</b>
              <small>ملفات STL, 3MF أو ZIP · حد أقصى 100 MB</small>
            </label>

            <div class="quote-options">
              <label>نوع الخامة
                <select id="material">
                  @forelse($filaments as $fil)
                    <option value="{{ $fil->id }}" data-cost="{{ $fil->cost_per_gram }}" data-name="{{ $fil->name }}">
                      {{ $fil->name }}
                    </option>
                  @empty
                    <option value="" data-cost="0.75" data-name="PLA+">PLA+ قياسي</option>
                  @endforelse
                </select>
              </label>
              <label>دقة الطباعة
                <select id="quality">
                  <option value="1">Standard — 0.20mm</option>
                  <option value="1.25">Fine — 0.12mm (أعلى دقة)</option>
                </select>
              </label>
            </div>

            <div class="color-row">
              <span>لون القطعة</span>
              <div class="swatches">
                <button class="swatch active" data-color="أخضر ليموني (Lime)" type="button" title="Lime"></button>
                <button class="swatch white" data-color="أبيض ناصع (White)" type="button" title="White"></button>
                <button class="swatch orange" data-color="برتقالي (Orange)" type="button" title="Orange"></button>
                <button class="swatch blue" data-color="أزرق رمادي (Blue)" type="button" title="Blue"></button>
              </div>
            </div>

            <!-- بيانات التواصل لطلب التسعير الفعلي -->
            <div id="quote-customer-fields" style="display:none; padding-top:14px; border-top:1px solid var(--line); margin-top:10px;">
              <p style="font-size:11px; color:var(--lime); margin:0 0 8px;">بيانات التواصل لمراجعة طلبك:</p>
              <div style="display:grid; grid-template-columns:1fr 1fr; gap:8px;">
                <label style="font-size:10px; color:#9aabb0;">اسمك
                  <input type="text" id="quote-name" placeholder="الاسم ثلاثي" style="width:100%; background:#102029; border:1px solid #243842; color:#dce6e7; padding:8px; font-size:11px; margin-top:4px;" />
                </label>
                <label style="font-size:10px; color:#9aabb0;">رقم الموبايل
                  <input type="tel" id="quote-phone" placeholder="01012345678" style="width:100%; background:#102029; border:1px solid #243842; color:#dce6e7; padding:8px; font-size:11px; margin-top:4px;" />
                </label>
              </div>
              <label style="font-size:10px; color:#9aabb0; display:block; margin-top:8px;">ملاحظات إضافية (اختياري)
                <textarea id="quote-notes" rows="2" placeholder="أي تفاصيل عن الاستخدام أو الكثافة (Infill)" style="width:100%; background:#102029; border:1px solid #243842; color:#dce6e7; padding:8px; font-size:11px; margin-top:4px; resize:none;"></textarea>
              </label>
            </div>

            <div class="estimate">
              <div><span>تقدير السعر المبدئي</span><strong id="quote-price">—</strong></div>
              <div><span>الوقت المتوقع</span><strong id="quote-time">—</strong></div>
              <div style="display:flex; flex-direction:column; gap:6px;">
                <button type="submit" class="button button-primary" id="quote-button" disabled style="padding:10px 12px; font-size:11px; justify-content:center;">
                  إرسال الطلب للاستوديو <span>↙</span>
                </button>
                <button type="button" class="button button-quiet" id="quote-whatsapp-button" style="display:none; padding:8px 10px; font-size:10px; justify-content:center; border-color:#25d366; color:#25d366;">
                  طلب عبر واتساب 💬
                </button>
              </div>
            </div>
          </form>
        </div>
      </section>

      <section class="process section" id="about">
        <div class="process-intro reveal"><span class="kicker">03 / THE TRIO METHOD</span><h2>ثلاثة أدوار.<br/><em>نتيجة واحدة متقنة.</em></h2></div>
        <div class="steps"><article class="step reveal"><span>01</span><div class="step-symbol design-symbol">✦</div><h3>نصمّم</h3><p>نحوّل الفكرة لرسم مدروس يناسب الاستخدام اليومي.</p></article><article class="step reveal-delay"><span>02</span><div class="step-symbol print-symbol"><i></i><i></i><i></i></div><h3>نطبع</h3><p>نعاير كل طبقة علشان الجودة ما تكونش صدفة.</p></article><article class="step reveal-delay-2"><span>03</span><div class="step-symbol finish-symbol">↗</div><h3>نكمّل</h3><p>تنظيف، تشطيب، وتغليف محترم لحد ما توصلك.</p></article></div>
      </section>

    </main>

    <footer>
      <div class="footer-mark"><span class="brand-mark"><i></i><i></i><i></i></span>TRIO</div>
      <p>ثلاثة أصدقاء يصنعون أشياء أفضل.</p>
      <div style="display:flex; gap:16px; align-items:center;">
        <span>© 2026 TRIO 3D PRINTING</span>
        <a href="{{ route('admin.login') }}" style="color:rgba(255,255,255,0.18); font-size:10px; text-decoration:none;">لوحة الإدارة</a>
      </div>
    </footer>

    <!-- سلة الطلبات -->
    <aside class="cart-drawer" id="cart-drawer" aria-hidden="true">
      <div class="drawer-head">
        <div><span class="kicker">YOUR BUILD</span><h3>سلة الطلبات</h3></div>
        <button id="close-cart">×</button>
      </div>
      <div class="cart-items" id="cart-items">
        <div class="empty-cart"><span>◌</span><p>لسه مفيش حاجة في السلة.</p></div>
      </div>
      <form id="checkout-form" class="checkout-form">
        <label>الاسم بالكامل
          <input name="customer_name" required autocomplete="name" placeholder="محمد أحمد" />
        </label>
        <label>رقم الموبايل
          <input name="phone" required autocomplete="tel" inputmode="tel" placeholder="01012345678" pattern="(\+?20|0)?1[0125][0-9]{8}" title="رقم هاتف مصري يبدأ بـ 01" />
        </label>
        <label>عنوان التوصيل بالتفصيل
          <input name="address" required autocomplete="street-address" placeholder="المحافظة، المدينة، اسم الشارع ورقم العمارة" />
        </label>
        <label>البريد الإلكتروني <small>اختياري</small>
          <input name="email" type="email" autocomplete="email" placeholder="name@example.com" />
        </label>
        <label>ملاحظات إضافية <small>اختياري</small>
          <textarea name="notes" rows="2" placeholder="أي تعليمات للتوصيل أو مواصفات خاصة"></textarea>
        </label>
        <p class="checkout-note">الدفع عند الاستلام أو فودافون كاش بعد تأكيد الطلب معك.</p>
        <div class="cart-footer">
          <p>الإجمالي <strong id="cart-total">EGP 0</strong></p>
          <button class="button button-primary" id="checkout" type="submit">تأكيد وإرسال الطلب <span>↙</span></button>
        </div>
      </form>
    </aside>

    <div class="backdrop" id="backdrop"></div>
    <div class="toast" id="toast">تمت إضافة القطعة للسلة <span>✓</span></div>

    <script>
      window.TRIO_CONFIG = {
        orderUrl: @json(route('orders.store')),
        quoteUrl: @json(route('custom_quotes.store')),
        csrf: @json(csrf_token()),
        whatsappPhone: '{{ config('app.whatsapp_phone', '201282537892') }}'
      };
    </script>
    <script src="{{ asset('trio.js') }}"></script>
    <script>
      // تفاعل سلايدر المنتجات (Carousel Navigation)
      const carousel = document.getElementById('product-carousel');
      const prevBtn = document.getElementById('carousel-prev');
      const nextBtn = document.getElementById('carousel-next');
      if (carousel && prevBtn && nextBtn) {
        prevBtn.addEventListener('click', () => {
          carousel.scrollBy({ left: 340, behavior: 'smooth' });
        });
        nextBtn.addEventListener('click', () => {
          carousel.scrollBy({ left: -340, behavior: 'smooth' });
        });
      }
    </script>
  </body>
</html>
