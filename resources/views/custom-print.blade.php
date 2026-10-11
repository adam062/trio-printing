<!doctype html>
<html lang="ar" dir="rtl">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="theme-color" content="#071019" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>اطبع فكرتك — TRIO 3D Printing</title>
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Alexandria:wght@400;500;600;700;800&family=DM+Mono:wght@400;500&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="@assetv('trio.css')" />
    <link rel="stylesheet" href="@assetv('store-overrides.css')" />
  </head>
  <body>
    <div class="noise"></div>
    <div class="glow glow-a"></div><div class="glow glow-b"></div>

    @include('partials.store-header', ['activeNav' => 'custom', 'showCart' => false])

    <main>
      @include('partials.custom-print-form')
    </main>

    @include('partials.store-footer')

    @include('partials.cart-popup')
    <div class="backdrop" id="backdrop"></div>
    <div class="toast" id="toast">تم استلام طلبك <span>✓</span></div>
    <script>
      window.TRIO_CONFIG = {
        quoteUrl: @json(route('custom_quotes.store')),
        csrf: @json(csrf_token()),
        whatsappPhone: @json(config('app.whatsapp_phone', '201282537892'))
      };
    </script>
    <script src="@assetv('trio.js')"></script>
  </body>
</html>
