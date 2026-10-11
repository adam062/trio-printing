@php
    $currentFilament = $currentFilament ?? null;
    $selectedColors = $selectedColors ?? [];
    $freeColors = $freeColors ?? '';
    $colorNames = collect($currentFilament?->colorInventory() ?? [])->pluck('name')->values();
@endphp
<div class="product-color-editor">
    <strong>ألوان المنتج (مرتبطة بألوان الخامة)</strong>
    <div class="color-checkbox-grid" data-color-options>
        @forelse($colorNames as $name)
            <label class="color-checkbox">
                <input type="checkbox" name="color_options[]" value="{{ $name }}" @checked(in_array($name, $selectedColors, true)) />
                <span>{{ $name }}</span>
            </label>
        @empty
            <small class="color-empty-note">لا توجد ألوان مُدارة لهذه الخامة بعد — أضف ألوان الخامة من صفحة «الخامات»، أو استخدم الألوان المخصصة بالأسفل.</small>
        @endforelse
    </div>
    <label>ألوان مخصصة (تُستخدم فقط إذا لم تكن الخامة تدير ألوانها)
        <input name="color_options_free" value="{{ $freeColors }}" placeholder="مثال: أسود، أبيض، برتقالي" />
    </label>
</div>
