@extends('admin.layout')

@section('title', 'TRIO · تعديل المنتج '.$product->name)

@section('content')
<div class="admin-shell">
    @include('admin.sidebar')

    <main class="admin-main">
        <header class="admin-topbar">
            <div>
                <p class="eyebrow">EDIT PRODUCT</p>
                <h1>تعديل منتج: {{ $product->name }}</h1>
            </div>
            <a class="store-link" href="{{ route('admin.products.index') }}">← عودة لقائمة المنتجات</a>
        </header>

        @if($errors->any())
            <div class="alert error">{{ $errors->first() }}</div>
        @endif

        <div style="max-width:700px; margin:0 auto;">
            @php
                $filamentColorMap = $filaments->mapWithKeys(fn ($f) => [$f->id => collect($f->colorInventory())->pluck('name')->values()]);
                $currentFilament = $filaments->firstWhere('id', old('filament_id', $product->filament_id));
                $selectedColors = old('color_options', $product->color_options ?? []);
                $freeColors = old('color_options_free', $product->filament?->hasManagedColorInventory() ? '' : implode('، ', $product->color_options ?? []));
            @endphp
            <article class="panel">
                <form method="POST" action="{{ route('admin.products.update', $product) }}" enctype="multipart/form-data" class="product-form" data-product-form data-filament-colors='@json($filamentColorMap)'>
                    @csrf
                    @method('PUT')

                    <label>اسم المنتج
                        <input name="name" value="{{ old('name', $product->name) }}" required maxlength="120" />
                    </label>

                    <label>وصف تسويقي قصير
                        <textarea name="description" rows="2" maxlength="500">{{ old('description', $product->description) }}</textarea>
                    </label>

                    <label>تفاصيل ومواصفات إضافية
                        <textarea name="details" rows="3" maxlength="2000">{{ old('details', $product->details) }}</textarea>
                    </label>

                    <div class="two-fields">
                        <label>الأبعاد التقديرية (الطول × العرض × الارتفاع)
                            <input name="dimensions" value="{{ old('dimensions', $product->dimensions) }}" placeholder="120 × 85 × 110 mm" />
                        </label>

                        <label>نوع الخامة
                            <select name="filament_id" data-filament-select style="background:#0b171f; border:1px solid #2b4049; color:#fff; padding:10px; border-radius:4px;">
                                @foreach($filaments as $f)
                                    <option value="{{ $f->id }}" data-cost="{{ $f->cost_per_gram }}" @selected(old('filament_id', $product->filament_id) == $f->id)>
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
                        <input name="color_label" value="{{ old('color_label', $product->color_label ?? 'اللون المتاح') }}" placeholder="مثال: لون الهيكل، لون القاعدة" />
                    </label>

                    <div class="two-fields">
                        <label>الوزن بالجرام
                            <input data-weight name="weight_grams" value="{{ old('weight_grams', (float) $product->weight_grams) }}" required type="number" min="1" step="0.1" />
                        </label>
                        <label>المضاعف
                            <select data-multiplier name="multiplier">
                                @for($x=1; $x<=10; $x++)
                                    <option value="{{ $x }}" @selected(old('multiplier', (int) $product->multiplier) == $x)>x{{ $x }}</option>
                                @endfor
                            </select>
                        </label>
                    </div>

                    <div class="price-preview">
                        <span>تكلفة الخامة المحسوبة: <b data-base-price>EGP {{ number_format($product->base_price, 2) }}</b></span>
                        <label class="final-price-field">سعر البيع النهائي:
                            <span class="price-input-wrap">
                                <input data-price-input name="price" type="number" min="0" step="0.01" value="{{ old('price', (float) $product->price) }}" placeholder="0.00" />
                                <b>ج.م</b>
                            </span>
                        </label>
                        <button type="button" class="price-auto-btn" data-price-auto hidden>↺ استخدام السعر المحسوب</button>
                        <small id="preview-calculation-note">السعر يتحدث تلقائياً حسب الخامة والوزن والمضاعف، ويمكنك تعديله يدوياً</small>
                    </div>

                    <div class="product-gallery-editor">
                        <strong>صور المنتج ({{ count($product->gallery()) }}) — الأولى هي الغلاف</strong>
                        <div class="gallery-thumbs">
                            @foreach($product->gallery() as $path)
                                <label class="gallery-thumb">
                                    <img src="@sasset($path)" alt="" />
                                    <span class="gallery-remove">
                                        <input type="checkbox" name="remove_images[]" value="{{ $path }}" />
                                        حذف
                                    </span>
                                </label>
                            @endforeach
                        </div>
                        <label class="image-upload">إضافة صور جديدة (يمكن اختيار أكثر من صورة)
                            <small>الصورة الأولى في الترتيب تصبح غلاف المنتج في الكتالوج</small>
                            <input data-image-input name="images[]" type="file" multiple accept="image/jpeg,image/png,image/webp" />
                        </label>
                        <div class="gallery-thumbs" data-image-preview-list></div>
                    </div>

                    <div style="display:flex; justify-content:space-between; margin-top:10px;">
                        <a href="{{ route('admin.products.index') }}" style="color:var(--muted); text-decoration:none; padding:12px; font-size:12px;">إلغاء</a>
                        <button class="lime-button" type="submit">حفظ التعديلات <span>✓</span></button>
                    </div>
                </form>
            </article>
        </div>
    </main>
</div>
@endsection
