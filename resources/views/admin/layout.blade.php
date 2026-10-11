<!doctype html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta name="theme-color" content="#08121a" />
    <title>@yield('title', 'TRIO Admin')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Alexandria:wght@400;500;600;700&family=DM+Mono:wght@400;500&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="@assetv('admin.css')" />
    <link rel="stylesheet" href="@assetv('admin-overrides.css')" />
</head>
<body>
    @yield('content')
    <script src="@assetv('admin.js')"></script>
</body>
</html>
