<!doctype html>
<html lang="ar" dir="rtl">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="theme-color" content="#071019" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>إتمام الطلب — TRIO</title>
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Alexandria:wght@400;500;600;700;800&family=DM+Mono:wght@400;500&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="@assetv('trio.css')" />
    <link rel="stylesheet" href="@assetv('store-overrides.css')" />
  </head>
  <body class="checkout-page">
    <div class="noise"></div>
    <div class="glow glow-a"></div><div class="glow glow-b"></div>

    @include('partials.store-header', ['activeNav' => null, 'showCart' => false])

    <main>
      <section class="checkout-hero">
        <span class="kicker">TRIO CHECKOUT</span>
        <h1>إتمام الطلب</h1>
        <p>راجع القطع المختارة واملأ بيانات التوصيل، وأكد طلبك. الدفع عند الاستلام أو فودافون كاش بعد التأكيد.</p>
      </section>

      <div class="checkout-layout" id="checkout-main">
        <section class="checkout-summary">
          <h2>ملخص الطلب</h2>
          <div class="cart-items" data-cart-items>
            <div class="empty-cart"><span>◌</span><p>لسه مفيش حاجة في السلة.</p></div>
          </div>
          <div class="summary-total">
            <span>الإجمالي</span>
            <strong data-cart-total>EGP 0</strong>
          </div>
          <a href="{{ route('catalog.index') }}" class="text-link">← مواصلة التسوق</a>
        </section>

        <form id="checkout-form" class="checkout-form checkout-page-form">
          <h2>بيانات التوصيل</h2>
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
            <textarea name="notes" rows="3" placeholder="أي تعليمات للتوصيل أو مواصفات خاصة"></textarea>
          </label>
          <p class="checkout-note">الدفع عند الاستلام أو فودافون كاش بعد تأكيد الطلب معك.</p>
          <button class="button button-primary" id="checkout" type="submit" disabled>تأكيد وإرسال الطلب <span>↙</span></button>
        </form>
      </div>

      <section class="checkout-success" id="checkout-success" hidden>
        <span class="success-mark">✓</span>
        <h2>تم استلام طلبك بنجاح!</h2>
        <p class="success-order">رقم الطلب: <strong id="success-order-number"></strong></p>
        <p class="success-total">الإجمالي: <strong id="success-total"></strong></p>
        <p class="success-note">سنتواصل معك قريبًا لتأكيد العنوان وموعد التوصيل.</p>
        <div class="success-actions">
          <a href="{{ route('storefront') }}" class="button button-primary">العودة للرئيسية</a>
          <a href="{{ route('catalog.index') }}" class="button button-quiet">مواصلة التسوق</a>
        </div>
      </section>
    </main>

    @include('partials.store-footer')

    <div class="toast" id="toast">تمت إضافة القطعة للسلة <span>✓</span></div>

    <script>
      window.TRIO_CONFIG = {
        orderUrl: @json(route('orders.store')),
        csrf: @json(csrf_token()),
        whatsappPhone: @json(config('app.whatsapp_phone', '201282537892'))
      };
    </script>
    <script src="@assetv('trio.js')"></script>
  </body>
</html>
