@extends('admin.layout')

@section('title', 'TRIO · إدارة المنتجات')

@section('content')
<div class="admin-shell">
    @include('admin.sidebar')

    <main class="admin-main">
        <header class="admin-topbar">
            <div>
                <p class="eyebrow">CATALOG & INVENTORY</p>
                <h1>إدارة منتجات المتجر <span>□</span></h1>
            </div>
            <a class="store-link" href="{{ route('catalog.index') }}" target="_blank">معاينة الكتالوج كعميل ↗</a>
        </header>

        @if(session('success'))
            <div class="alert success">{{ session('success') }}</div>
        @endif
        @if($errors->any())
            <div class="alert error">{{ $errors->first() }}</div>
        @endif

        <div class="admin-grid product-management-grid">
            <!-- نموذج إضافة منتج جديد -->
            <article class="panel add-product-panel">
                <div class="panel-title">
                    <div>
                        <p class="eyebrow">PRODUCT BUILDER</p>
                        <h2>إضافة منتج جديد للكتالوج</h2>
                    </div>
                    <span class="formula-badge">الوزن × الخامة × المضاعف</span>
                </div>

                @php
                    $filamentColorMap = $filaments->mapWithKeys(fn ($f) => [$f->id => collect($f->colorInventory())->pluck('name')->values()]);
                    $currentFilament = $filaments->firstWhere('id', old('filament_id'));
                    $selectedColors = old('color_options', []);
                    $freeColors = old('color_options_free', '');
                @endphp
                <form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data" class="product-form" data-product-form data-filament-colors='@json($filamentColorMap)'>
                    @csrf
                    <label>اسم المنتج
                        <input name="name" value="{{ old('name') }}" required maxlength="120" placeholder="مثال: Orbit Stand" />
                    </label>

                    <label>وصف تسويقي قصير
                        <textarea name="description" rows="2" maxlength="500" placeholder="وصف جذاب يظهر في كارت المنتج">{{ old('description') }}</textarea>
                    </label>

                    <label>تفاصيل ومواصفات إضافية (اختياري)
                        <textarea name="details" rows="2" maxlength="2000" placeholder="مواصفات الطباعة، الاستخدام، نسبة الحشو Infill، أو نصائح التركيب">{{ old('details') }}</textarea>
                    </label>

                    <div class="two-fields">
                        <label>الأبعاد التقديرية (الطول × العرض × الارتفاع)
                            <input name="dimensions" value="{{ old('dimensions') }}" placeholder="مثال: 120 × 85 × 110 mm" />
                        </label>

                        <label>نوع الخامة المستخدمة
                            <select name="filament_id" data-filament-select style="background:#0b171f; border:1px solid #2b4049; color:#fff; padding:10px; border-radius:4px;">
                                @foreach($filaments as $f)
                                    <option value="{{ $f->id }}" data-cost="{{ $f->cost_per_gram }}" @selected(old('filament_id') == $f->id)>
                                        {{ $f->name }} ({{ number_format($f->cost_per_gram, 3) }} ج.م/g)
                                    </option>
                                @endforeach
                            </select>
                        </label>
                    </div>

                    @include('admin.products._color-options', [
                        'currentFilament' => $currentFilament,
                        'selectedColors' => $selectedColors,
                        'freeColors' => $freeColors,
                    ])

                    <label>تسمية خيار اللون للعميل
                        <input name="color_label" value="{{ old('color_label', 'اللون المتاح') }}" placeholder="مثال: لون الهيكل، لون القاعدة، أو اللون" />
                    </label>

                    <div class="two-fields">
                        <label>الوزن بالجرام
                            <input data-weight name="weight_grams" value="{{ old('weight_grams', 90) }}" required type="number" min="1" step="0.1" placeholder="90" />
                        </label>
                        <label>المضاعف (سعر التشغيل والربح)
                            <select data-multiplier name="multiplier">
                                @for($x=1; $x<=10; $x++)
                                    <option value="{{ $x }}" @selected(old('multiplier', 4) == $x)>x{{ $x }} ({{ $x*100 }}%)</option>
                                @endfor
                            </select>
                        </label>
                    </div>

                    <div class="price-preview">
                        <span>تكلفة الخامة: <b data-base-price>EGP 0.00</b></span>
                        <label class="final-price-field">سعر البيع النهائي:
                            <span class="price-input-wrap">
                                <input data-price-input name="price" type="number" min="0" step="0.01" value="{{ old('price') }}" placeholder="0.00" />
                                <b>ج.م</b>
                            </span>
                        </label>
                        <button type="button" class="price-auto-btn" data-price-auto hidden>↺ استخدام السعر المحسوب</button>
                        <small id="preview-calculation-note">الحساب المباشر: وزن القطعة × تكلفة جرام الخامة × المضاعف</small>
                    </div>

                    <label class="image-upload">صور المنتج (يمكن اختيار أكثر من صورة)
                        <small>JPG, PNG أو WEBP · حتى 4MB لكل صورة · الأولى هي الغلاف</small>
                        <input data-image-input name="images[]" type="file" multiple accept="image/jpeg,image/png,image/webp" />
                    </label>
                    <div class="gallery-thumbs" data-image-preview-list></div>

                    <button class="lime-button" type="submit">نشر المنتج في المتجر <span>+</span></button>
                </form>
            </article>

            <!-- قائمة المنتجات الحالية -->
            <article class="panel">
                <div class="panel-title">
                    <div>
                        <p class="eyebrow">CATALOG</p>
                        <h2>المنتجات المنشورة ({{ $products->total() }})</h2>
                    </div>
                </div>

                <form method="GET" action="{{ route('admin.products.index') }}" style="display:flex; gap:8px; margin-bottom:15px;">
                    <input name="search" value="{{ $currentSearch }}" placeholder="بحث باسم المنتج..." style="flex:1; background:#08131a; border:1px solid #233944; color:#fff; padding:8px 12px; border-radius:4px; font-size:11px;" />
                    <button type="submit" style="background:#1b2f3a; color:var(--lime); border:1px solid #365565; border-radius:4px; padding:0 14px; font-size:11px; cursor:pointer;">بحث</button>
                    @if($currentSearch)
                        <a href="{{ route('admin.products.index') }}" style="padding:8px; color:var(--muted); text-decoration:none; font-size:11px;">إلغاء</a>
                    @endif
                </form>

                <div class="product-admin-list">
                    @forelse($products as $product)
                        <div class="product-admin-row product-management-row" style="display:grid; grid-template-columns: 48px 1fr auto auto; gap:12px; align-items:center;">
                            <div class="admin-product-thumb">
                                @if($product->image_path)
                                    <img src="@sasset($product->image_path)" alt="" />
                                @else
                                    <span>▣</span>
                                @endif
                            </div>
                            <div>
                                <h3 style="margin:0 0 3px 0;">{{ $product->name }}</h3>
                                <p style="margin:0; font-size:10px; color:var(--muted);">
                                    {{ $product->weight_grams }}g
                                    @if($product->dimensions) · {{ $product->dimensions }} @endif
                                    · <span style="color:var(--lime);">{{ $product->filament?->name ?? 'PLA' }}</span>
                                </p>
                            </div>
                            <strong style="font-size:12px; font-family:var(--mono);">EGP {{ number_format($product->price, 2) }}</strong>

                            <div style="display:flex; gap:6px;">
                                <!-- تعديل المنتج -->
                                <a href="{{ route('admin.products.edit', $product) }}" title="تعديل المنتج" style="background:#132530; border:1px solid #2d4857; color:#fff; padding:4px 8px; border-radius:3px; text-decoration:none; font-size:10px;">
                                    تعديل
                                </a>

                                <!-- تفعيل / إخفاء -->
                                <form method="POST" action="{{ route('admin.products.toggle', $product) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" title="{{ $product->is_active ? 'إخفاء من المتجر' : 'تفعيل بالمتجر' }}" style="background:none; border:1px solid #2a414c; color:{{ $product->is_active ? 'var(--lime)' : '#839599' }}; padding:4px 7px; border-radius:3px; cursor:pointer; font-size:10px;">
                                        {{ $product->is_active ? 'ظاهر' : 'مخفي' }}
                                    </button>
                                </form>

                                <!-- حذف -->
                                <form method="POST" action="{{ route('admin.products.destroy', $product) }}" onsubmit="return confirm('هل أنت متأكد من حذف هذا المنتج؟');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" title="حذف المنتج" style="background:none; border:1px solid #ff8e7a44; color:#ff8e7a; padding:4px 7px; border-radius:3px; cursor:pointer; font-size:10px;">
                                        ×
                                    </button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="empty-state">لا توجد منتجات مسجلة.</div>
                    @endforelse
                </div>

                <div style="margin-top:15px;">
                    {{ $products->links() }}
                </div>
            </article>
        </div>
    </main>
</div>
@endsection
