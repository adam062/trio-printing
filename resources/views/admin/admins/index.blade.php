@extends('admin.layout')

@section('title', 'TRIO · إدارة المشرفين والصلاحيات')

@section('content')
<div class="admin-shell">
    @include('admin.sidebar')

    <main class="admin-main">
        <header class="admin-topbar">
            <div>
                <p class="eyebrow">SECURITY & ACCESS CONTROL</p>
                <h1>فريق الإدارة والصلاحيات <span>👥</span></h1>
            </div>
            <a class="store-link" href="{{ route('admin.dashboard') }}">← عودة لنظرة عامة</a>
        </header>

        @if(session('success'))
            <div class="alert success">{{ session('success') }}</div>
        @endif
        @if($errors->any())
            <div class="alert error">{{ $errors->first() }}</div>
        @endif

        <div style="display:grid; grid-template-columns: 1fr 1.4fr; gap:25px;">
            <!-- إضافة حساب مشرف جديد -->
            <article class="panel">
                <div class="panel-title">
                    <div>
                        <p class="eyebrow">ADD TEAM MEMBER</p>
                        <h2>+ إنشاء حساب مشرف جديد</h2>
                    </div>
                </div>

                <form method="POST" action="{{ route('admin.admins.store') }}" class="product-form">
                    @csrf
                    <label>الاسم الظاهر
                        <input name="name" required placeholder="اسم المشرف" />
                    </label>

                    <label>البريد الإلكتروني (لتسجيل الدخول)
                        <input name="email" type="email" required placeholder="staff@example.com" />
                    </label>

                    <label>كلمة المرور
                        <input name="password" type="password" required minlength="8" placeholder="8 أحرف على الأقل" />
                    </label>

                    <label>الدور (Role)
                        <select name="role" id="role-select" style="background:#09151e; border:1px solid #273e4b; color:#fff; padding:10px; border-radius:4px;">
                            <option value="staff">مشرف مخصص (Custom Permissions)</option>
                            <option value="super_admin">مدير رئيسي (Super Admin — كامل الصلاحيات)</option>
                        </select>
                    </label>

                    <!-- تحديد الصلاحيات للمشرف -->
                    <div id="permissions-container" style="background:#04090e; padding:12px; border-radius:6px; border:1px solid #1a2e3a; margin-top:8px;">
                        <span style="font-size:11px; color:var(--lime); display:block; margin-bottom:8px; font-weight:bold;">
                            تحديد الصلاحيات المسموح بها لهذا الحساب:
                        </span>
                        <div style="display:grid; gap:8px;">
                            @foreach($availablePermissions as $key => $title)
                                <label style="display:flex; align-items:center; gap:8px; font-size:11px; color:#dbe6e8; cursor:pointer;">
                                    <input type="checkbox" name="permissions[]" value="{{ $key }}" checked style="width:auto; margin:0;" />
                                    <span>{{ $title }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <button type="submit" class="lime-button" style="margin-top:12px;">إنشاء الحساب وتفعيل الصلاحيات <span>+</span></button>
                </form>
            </article>

            <!-- قائمة المشرفين الحاليين -->
            <article class="panel">
                <div class="panel-title">
                    <div>
                        <p class="eyebrow">ACTIVE ADMINS</p>
                        <h2>المشرفون الحاليون ({{ $admins->count() }})</h2>
                    </div>
                </div>

                <div style="display:grid; gap:12px;">
                    @foreach($admins as $admin)
                        <div style="background:#09141c; padding:16px; border-radius:6px; border:1px solid var(--line); display:flex; justify-content:space-between; align-items:flex-start;">
                            <div>
                                <div style="display:flex; align-items:center; gap:8px;">
                                    <strong style="font-size:15px; color:#fff;">{{ $admin->name }}</strong>
                                    <span style="font-size:9px; font-family:var(--mono); background:#152834; color:var(--lime); padding:2px 6px; border-radius:3px;">
                                        {{ $admin->isSuperAdmin() ? 'Super Admin' : 'مشرف مخصص' }}
                                    </span>
                                    @if($admin->id === auth()->id())
                                        <small style="color:var(--lime); font-size:10px;">(أنت)</small>
                                    @endif
                                </div>
                                <small style="color:var(--muted); font-size:11px; display:block; margin:2px 0 8px 0;">{{ $admin->email }}</small>

                                <!-- عرض الصلاحيات كشارات -->
                                <div style="display:flex; gap:6px; flex-wrap:wrap;">
                                    @if($admin->isSuperAdmin())
                                        <span style="background:#1a3123; color:#a3f350; font-size:9px; padding:2px 8px; border-radius:3px;">
                                            كامل الصلاحيات (جميع الأقسام)
                                        </span>
                                    @else
                                        @forelse($admin->permissions ?? [] as $perm)
                                            <span style="background:#0c1e28; color:#95cce0; font-size:9px; padding:2px 6px; border-radius:3px;">
                                                {{ $perm === 'orders' ? 'الطلبات' : ($perm === 'products' ? 'المنتجات' : ($perm === 'filaments' ? 'الخامات' : 'التصاميم')) }}
                                            </span>
                                        @empty
                                            <span style="color:var(--muted); font-size:9px;">بدون صلاحيات إضافية</span>
                                        @endforelse
                                    @endif
                                </div>
                            </div>

                            @if($admin->id !== auth()->id() && $admin->id !== 1)
                                <form method="POST" action="{{ route('admin.admins.destroy', $admin) }}" onsubmit="return confirm('هل أنت متأكد من حذف حساب المشرف {{ $admin->name }}؟');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" style="background:none; border:1px solid #ff8e7a44; color:#ff8e7a; padding:4px 8px; border-radius:3px; cursor:pointer; font-size:11px;">
                                        حذف المشرف
                                    </button>
                                </form>
                            @endif
                        </div>
                    @endforeach
                </div>
            </article>
        </div>
    </main>
</div>

<script>
    const roleSelect = document.getElementById('role-select');
    const permContainer = document.getElementById('permissions-container');
    if (roleSelect && permContainer) {
        roleSelect.addEventListener('change', () => {
            permContainer.style.display = roleSelect.value === 'super_admin' ? 'none' : 'block';
        });
    }
</script>
@endsection
