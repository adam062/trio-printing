<aside class="admin-sidebar">
    <a class="admin-brand" href="{{ route('storefront') }}">
        <span class="brand-bars"><i></i><i></i><i></i></span>TRIO
    </a>
    <p class="sidebar-label">لوحة التشغيل</p>

    <nav class="admin-sidebar-nav" aria-label="أقسام لوحة الإدارة">
        <a class="side-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
            ◫ <span>نظرة عامة</span>
        </a>

        @if(auth()->user()->canAccess('products'))
            <a class="side-link {{ request()->routeIs('admin.products.*') ? 'active' : '' }}" href="{{ route('admin.products.index') }}">
                □ <span>إدارة المنتجات</span>
            </a>
        @endif

        @if(auth()->user()->canAccess('filaments'))
            <a class="side-link {{ request()->routeIs('admin.filaments.*') ? 'active' : '' }}" href="{{ route('admin.filaments.index') }}">
                🧵 <span>خامات الفيلمنت</span>
            </a>
        @endif

        @if(auth()->user()->canAccess('orders'))
            <a class="side-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}" href="{{ route('admin.orders.index') }}">
                ◌ <span>طلبات المتجر</span>
            </a>
        @endif

        @if(auth()->user()->canAccess('quotes'))
            <a class="side-link {{ request()->routeIs('admin.custom_quotes.*') ? 'active' : '' }}" href="{{ route('admin.custom_quotes.index') }}">
                ↗ <span>تصاميم مخصصة</span>
            </a>
        @endif

        @if(auth()->user()->isSuperAdmin())
            <a class="side-link {{ request()->routeIs('admin.admins.*') ? 'active' : '' }}" href="{{ route('admin.admins.index') }}">
                👥 <span>المشرفين والصلاحيات</span>
            </a>
        @endif
    </nav>

    <div class="sidebar-bottom">
        <div style="display:flex; justify-content:space-between; align-items:center;">
            <strong>{{ auth()->user()->name }}</strong>
            <span style="font-size:9px; font-family:var(--mono); background:#152834; color:var(--lime); padding:2px 6px; border-radius:3px;">
                {{ auth()->user()->isSuperAdmin() ? 'Super Admin' : 'مشرف' }}
            </span>
        </div>
        <small style="color: var(--muted); display:block; margin-top:3px; font-size:10px;">{{ auth()->user()->email }}</small>

        <form method="POST" action="{{ route('admin.logout') }}" style="margin-top:14px;">
            @csrf
            <button type="submit">تسجيل الخروج ↙</button>
        </form>
    </div>
</aside>
