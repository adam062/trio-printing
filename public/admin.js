const productForm = document.querySelector('[data-product-form]');

if (productForm) {
    const weight = productForm.querySelector('[data-weight]');
    const multiplier = productForm.querySelector('[data-multiplier]');
    const filamentSelect = productForm.querySelector('[data-filament-select]');
    const basePrice = productForm.querySelector('[data-base-price]');
    const priceInput = productForm.querySelector('[data-price-input]');
    const priceAutoBtn = productForm.querySelector('[data-price-auto]');
    const calculationNote = document.getElementById('preview-calculation-note');
    const imageInput = productForm.querySelector('[data-image-input]');
    const imagePreviewList = productForm.querySelector('[data-image-preview-list]');
    const colorOptionsBox = productForm.querySelector('[data-color-options]');
    let manualPrice = false;
    const filamentColors = (() => {
        try {
            return JSON.parse(productForm.dataset.filamentColors || '{}');
        } catch {
            return {};
        }
    })();
    const money = (amount) => `EGP ${Number(amount).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
    const syncAutoButton = () => {
        if (priceAutoBtn) {
            priceAutoBtn.hidden = !manualPrice;
        }
    };

    const rebuildColorOptions = () => {
        if (!colorOptionsBox || !filamentSelect) return;

        const checked = [...colorOptionsBox.querySelectorAll('input:checked')].map((input) => input.value);
        const filamentId = filamentSelect.value;
        const names = filamentColors[filamentId] || [];

        colorOptionsBox.innerHTML = names.length
            ? names.map((name) => `
                <label class="color-checkbox">
                    <input type="checkbox" name="color_options[]" value="${name}" ${checked.includes(name) ? 'checked' : ''} />
                    <span>${name}</span>
                </label>
            `).join('')
            : '<small class="color-empty-note">لا توجد ألوان مُدارة لهذه الخامة بعد — أضف ألوان الخامة من صفحة «الخامات»، أو استخدم الألوان المخصصة بالأسفل.</small>';
    };

    const calculate = () => {
        const grams = Number(weight.value) || 0;
        const factor = Number(multiplier.value) || 1;
        
        let gramCost = 0.675;
        let filamentName = 'خامة قياسية';
        if (filamentSelect && filamentSelect.selectedOptions.length) {
            const opt = filamentSelect.selectedOptions[0];
            gramCost = Number(opt.dataset.cost) || 0.675;
            filamentName = opt.textContent.split('(')[0].trim();
        }

        const material = grams * gramCost;
        const total = material * factor;

        basePrice.textContent = money(material);

        if (priceInput && !manualPrice) {
            priceInput.value = total.toFixed(2);
        }

        if (calculationNote) {
            calculationNote.textContent = `${grams}g × ${gramCost.toFixed(3)} ج.م/جرام (${filamentName}) × مضاعف ${factor} = ${money(total)}`;
        }
    };

    const detectManualPrice = () => {
        if (!priceInput) {
            return;
        }
        const current = Number(priceInput.value);
        if (priceInput.value === '' || Number.isNaN(current)) {
            manualPrice = false;

            return;
        }
        const grams = Number(weight.value) || 0;
        const factor = Number(multiplier.value) || 1;
        let gramCost = 0.675;
        if (filamentSelect && filamentSelect.selectedOptions.length) {
            gramCost = Number(filamentSelect.selectedOptions[0].dataset.cost) || 0.675;
        }
        const calculated = grams * gramCost * factor;
        manualPrice = Math.abs(current - calculated) > 0.01;
    };

    weight.addEventListener('input', calculate);
    multiplier.addEventListener('change', calculate);
    if (filamentSelect) {
        filamentSelect.addEventListener('change', calculate);
        filamentSelect.addEventListener('change', rebuildColorOptions);
    }

    if (priceInput) {
        priceInput.addEventListener('input', () => {
            manualPrice = true;
            syncAutoButton();
        });
    }

    if (priceAutoBtn) {
        priceAutoBtn.addEventListener('click', () => {
            manualPrice = false;
            syncAutoButton();
            calculate();
        });
    }

    if (imageInput && imagePreviewList) {
        imageInput.addEventListener('change', () => {
            imagePreviewList.innerHTML = '';
            [...imageInput.files].forEach((file) => {
                const thumb = document.createElement('div');
                thumb.className = 'gallery-thumb';
                const img = document.createElement('img');
                img.src = URL.createObjectURL(file);
                img.alt = 'معاينة صورة جديدة';
                thumb.append(img);
                imagePreviewList.append(thumb);
            });
        });
    }

    detectManualPrice();
    syncAutoButton();
    calculate();
}
