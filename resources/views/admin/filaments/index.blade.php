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

        <div class="filaments-grid">
            <!-- إضافة خامة جديدة -->
            <article class="panel">
                <div class="panel-title">
                    <div>
                        <p class="eyebrow">ADD MATERIAL</p>
                        <h2>+ إضافة بكرة خامة جديدة</h2>
                    </div>
                </div>

                <form method="POST" action="{{ route('admin.filaments.store') }}" class="product-form" data-colors-form>
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

                    <div class="filament-inventory-editor">
                        <strong>ألوان الخامة المتاحة</strong>
                        <div class="filament-color-rows" data-color-rows></div>
                        <button type="button" class="filament-add-color" data-add-color>+ إضافة لون</button>
                    </div>

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
                                    <small style="color:{{ $filament->is_active ? 'var(--lime)' : '#ff8e7a' }};">
                                        {{ $filament->is_active ? 'متاح' : 'نفد المخزون' }}
                                    </small>
                                    <small style="background:#13242e; color:var(--muted); padding:3px 7px; border-radius:3px; font-size:10px;">
                                        {{ $filament->products_count }} منتج مرتبط
                                    </small>
                                </div>

                                <div style="text-align:left; font-family:var(--mono); color:var(--lime); font-size:13px;">
                                    تكلفة الجرام: <strong>EGP {{ number_format($filament->cost_per_gram, 3) }}</strong>
                                </div>
                            </div>

                            <!-- فورم تعديل سعر البكرة -->
                            <form method="POST" action="{{ route('admin.filaments.update', $filament) }}" class="filament-update-form" data-colors-form>
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="color_hint" value="{{ $filament->color_hint }}">

                                <label>اسم الخامة والماركة
                                    <input name="name" value="{{ $filament->name }}" required placeholder="مثال: PLA+ Sunlu، PETG، أو TPU" />
                                </label>

                                <div class="filament-price-fields">
                                    <label>سعر البكرة (ج.م)
                                        <input name="spool_price" type="number" step="0.5" value="{{ (float) $filament->spool_price }}" required />
                                    </label>
                                    <label>وزن البكرة (g)
                                        <input name="spool_weight_grams" type="number" step="10" value="{{ (int) $filament->spool_weight_grams }}" required />
                                    </label>
                                </div>

                                <label class="filament-stock-toggle">
                                    <input type="hidden" name="is_active" value="0" />
                                    <input type="checkbox" name="is_active" value="1" @checked($filament->is_active) />
                                    الخامة متاحة للطلبات
                                </label>

                                <div class="filament-inventory-editor">
                                    <strong>ألوان الخامة ومخزون كل لون</strong>
                                    <div class="filament-color-rows" data-color-rows>
                                        @foreach($filament->colorInventory() as $index => $color)
                                            <div class="filament-color-row">
                                                <input name="colors[{{ $index }}][name]" value="{{ $color['name'] }}" maxlength="80" placeholder="اسم اللون" aria-label="اسم اللون" />
                                                <label>
                                                    <input type="checkbox" name="colors[{{ $index }}][in_stock]" value="1" @checked($color['in_stock']) />
                                                    متوفر
                                                </label>
                                                <button type="button" data-remove-color aria-label="حذف اللون">×</button>
                                            </div>
                                        @endforeach
                                    </div>
                                    <button type="button" class="filament-add-color" data-add-color>+ إضافة لون</button>
                                </div>

                                <button type="submit" class="filament-save-button">حفظ بيانات الخامة ↻</button>
                            </form>

                            <form method="POST" action="{{ route('admin.filaments.destroy', $filament) }}" onsubmit="return confirm('حذف نوع الخامة؟ المنتجات المرتبطة ستبقى في الكتالوج بدون خامة مرتبطة.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="filament-delete-button">حذف نوع الخامة</button>
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
<script>
    document.querySelectorAll('[data-colors-form]').forEach((form) => {
        const rows = form.querySelector('[data-color-rows]');
        const addButton = form.querySelector('[data-add-color]');

        const addColorRow = () => {
            const index = rows.querySelectorAll('.filament-color-row').length;
            const row = document.createElement('div');
            row.className = 'filament-color-row';
            row.innerHTML = `
                <input name="colors[${index}][name]" maxlength="80" placeholder="اسم اللون" aria-label="اسم اللون" required />
                <label><input type="checkbox" name="colors[${index}][in_stock]" value="1" checked /> متوفر</label>
                <button type="button" data-remove-color aria-label="حذف اللون">×</button>
            `;
            rows.append(row);
        };

        addButton.addEventListener('click', addColorRow);
        form.addEventListener('click', (event) => {
            if (event.target.closest('[data-remove-color]')) {
                event.target.closest('.filament-color-row').remove();
                rows.querySelectorAll('.filament-color-row').forEach((row, index) => {
                    row.querySelector('input[name$="[name]"]').name = `colors[${index}][name]`;
                    row.querySelector('input[type="checkbox"]').name = `colors[${index}][in_stock]`;
                });
            }
        });
    });
</script>
@endsection
