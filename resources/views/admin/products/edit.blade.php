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
            <article class="panel">
                <form method="POST" action="{{ route('admin.products.update', $product) }}" enctype="multipart/form-data" class="product-form" data-product-form>
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

                    <div class="two-fields">
                        <label>خيارات ألوان القطعة (افصل بفاصلة)
                            <input name="color_options" value="{{ old('color_options', is_array($product->color_options) ? implode('، ', $product->color_options) : '') }}" placeholder="مثال: أسود، أبيض، برتقالي، أخضر" />
                        </label>
                        <label>تسمية خيار اللون للعميل
                            <input name="color_label" value="{{ old('color_label', $product->color_label ?? 'اللون المتاح') }}" placeholder="مثال: لون الهيكل، لون القاعدة" />
                        </label>
                    </div>

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
                        <strong>سعر البيع النهائي: <b data-selling-price>EGP {{ number_format($product->price, 2) }}</b></strong>
                        <small id="preview-calculation-note">السعر يتحدث تلقائياً حسب الخامة والوزن والمضاعف</small>
                    </div>

                    <div style="display:flex; gap:15px; align-items:center;">
                        @if($product->image_path)
                            <div style="width:70px; height:70px; border-radius:4px; overflow:hidden; background:#10202a; border:1px solid #2b4049;">
                                <img src="{{ asset('storage/'.$product->image_path) }}" alt="" style="width:100%; height:100%; object-fit:cover;" />
                            </div>
                        @endif
                        <label class="image-upload" style="flex:1;">تغيير صورة المنتج
                            <small>اتركها فارغة للاحتفاظ بالصورة الحالية</small>
                            <input data-image-input name="image" type="file" accept="image/jpeg,image/png,image/webp" />
                            <img data-image-preview hidden alt="معاينة الصورة" />
                        </label>
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
