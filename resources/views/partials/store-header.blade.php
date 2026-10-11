@php
    $activeNav = $activeNav ?? null;

    $navItems = [
        'home' => [
            'href' => $activeNav === 'home' ? '#top' : route('storefront'),
            'label' => 'الرئيسية',
        ],
        'catalog' => [
            'href' => route('catalog.index'),
            'label' => 'المتجر والكتالوج',
        ],
        'custom' => [
            'href' => route('custom_print.create'),
            'label' => 'اطبع فكرتك',
        ],
        'about' => [
            'href' => ($activeNav === 'home' ? '' : route('storefront')).'#about',
            'label' => 'فكرة TRIO',
        ],
    ];
@endphp
<header class="site-header">
    <a class="brand" href="{{ route('storefront') }}" aria-label="TRIO homepage">
        <span class="brand-mark"><i></i><i></i><i></i></span><span>TRIO</span>
    </a>

    <nav class="nav-links" id="primary-nav" aria-label="التنقل الرئيسي">
        @foreach ($navItems as $key => $item)
            <a
                href="{{ $item['href'] }}"
                @class(['is-active' => $activeNav === $key])
                @if ($activeNav === $key) aria-current="page" @endif
            >{{ $item['label'] }}</a>
        @endforeach
    </nav>

    <div class="header-actions">
        <button class="nav-toggle" id="nav-toggle" type="button" aria-label="فتح القائمة" aria-expanded="false" aria-controls="primary-nav">
            <span></span><span></span><span></span>
        </button>
    </div>
</header>
