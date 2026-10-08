@extends('admin.layout')

@section('title', 'TRIO · خامات وبكرات الفيلمنت')

@section('content')
<div class="admin-shell">
    @include('admin.sidebar')

    <main class="admin-main">
        <header class="admin-topbar">
            <div>
                <p class="eyebrow">MATERIALS & SPOOLS</p>
                <h1>خامات وبكرات الفيلمنت (Filament) <span>🧵</span></h1>
            </div>
            <a class="store-link" href="{{ route('admin.dashboard') }}">← عودة لنظرة عامة</a>
        </header>

        @if(session('success'))
            <div class="alert success">{{ session('success') }}</div>
        @endif
        @if($errors->any())
            <div class="alert error">{{ $errors->first() }}</div>
        @endif

        <div class="filaments-grid" style="display:grid; grid-template-columns: 1fr 1.6fr; gap:25px;">
            <!-- إضافة خامة جديدة -->
            <article class="panel">
                <div class="panel-title">
                    <div>
                        <p class="eyebrow">ADD MATERIAL</p>
                        <h2>+ إضافة بكرة خامة جديدة</h2>
                    </div>
                </div>

                <form method="POST" action="{{ route('admin.filaments.store') }}" class="product-form">
                    @csrf
                    <label>اسم الخامة والماركة
                        <input name="name" required placeholder="مثال: PLA+ Sunlu، PETG، أو TPU" />
                    </label>

                    <div class="two-fields">
                        <label>سعر البكرة (ج.م)
                            <input name="spool_price" type="number" step="0.5" required placeholder="مثال: 750" />
                        </label>
                        <label>وزن البكرة (جرام)
                            <input name="spool_weight_grams" type="number" step="10" value="1000" required placeholder="1000" />
                        </label>
                    </div>

                    <label>اللون المميز للخامة
                        <input name="color_hint" type="text" placeholder="#c8f228 أو أخضر أو أبيض" value="#c8f228" />
                    </label>

                    <button type="submit" class="lime-button" style="margin-top:8px;">حفظ الخامة <span>+</span></button>
                </form>
            </article>

            <!-- قائمة الخامات مع تعديل سريع لسعر البكرة -->
            <article class="panel">
                <div class="panel-title">
                    <div>
                        <p class="eyebrow">ACTIVE MATERIALS</p>
                        <h2>الخامات المسجلة ({{ $filaments->count() }})</h2>
                    </div>
                    <span class="formula-badge">سعر البكرة ÷ الوزن = تكلفة الجرام</span>
                </div>

                <div style="display:grid; gap:12px;">
                    @forelse($filaments as $filament)
                        <div style="background:#09141c; padding:16px 20px; border-radius:6px; border:1px solid var(--line); display:flex; flex-direction:column; gap:12px;">
                            <div style="display:flex; justify-content:space-between; align-items:center;">
                                <div style="display:flex; align-items:center; gap:10px;">
                                    <span style="display:inline-block; width:14px; height:14px; border-radius:50%; background:{{ $filament->color_hint ?: '#c8f228' }};"></span>
                                    <strong style="font-size:15px; color:#fff;">{{ $filament->name }}</strong>
                                    <small style="background:#13242e; color:var(--muted); padding:3px 7px; border-radius:3px; font-size:10px;">
                                        {{ $filament->products_count }} منتج مرتبط
                                    </small>
                                </div>

                                <div style="text-align:left; font-family:var(--mono); color:var(--lime); font-size:13px;">
                                    تكلفة الجرام: <strong>EGP {{ number_format($filament->cost_per_gram, 3) }}</strong>
                                </div>
                            </div>

                            <!-- فورم تعديل سعر البكرة -->
                            <form method="POST" action="{{ route('admin.filaments.update', $filament) }}" style="display:flex; gap:10px; align-items:flex-end;">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="name" value="{{ $filament->name }}">
                                <input type="hidden" name="color_hint" value="{{ $filament->color_hint }}">

                                <div style="flex:1;">
                                    <label style="font-size:10px; color:var(--muted); display:block; margin-bottom:4px;">سعر البكرة (ج.م):</label>
                                    <input name="spool_price" type="number" step="0.5" value="{{ (float) $filament->spool_price }}" style="background:#04090e; border:1px solid #233944; color:#fff; padding:7px 10px; border-radius:4px; font-family:var(--mono); width:100%;" required />
                                </div>

                                <div style="width:110px;">
                                    <label style="font-size:10px; color:var(--muted); display:block; margin-bottom:4px;">وزن البكرة (g):</label>
                                    <input name="spool_weight_grams" type="number" step="10" value="{{ (int) $filament->spool_weight_grams }}" style="background:#04090e; border:1px solid #233944; color:#fff; padding:7px 10px; border-radius:4px; font-family:var(--mono); width:100%;" required />
                                </div>

                                <button type="submit" style="background:#1b2f3a; color:var(--lime); border:1px solid #365565; border-radius:4px; padding:8px 14px; font-size:11px; cursor:pointer; height:34px; font-weight:bold;">
                                    تحديث السعر ↻
                                </button>
                            </form>
                        </div>
                    @empty
                        <div class="empty-state">لا توجد خامات مسجلة حتى الآن.</div>
                    @endforelse
                </div>
            </article>
        </div>
    </main>
</div>
@endsection
