const productForm = document.querySelector('[data-product-form]');

if (productForm) {
    const weight = productForm.querySelector('[data-weight]');
    const multiplier = productForm.querySelector('[data-multiplier]');
    const filamentSelect = productForm.querySelector('[data-filament-select]');
    const basePrice = productForm.querySelector('[data-base-price]');
    const sellingPrice = productForm.querySelector('[data-selling-price]');
    const calculationNote = document.getElementById('preview-calculation-note');
    const imageInput = productForm.querySelector('[data-image-input]');
    const imagePreview = productForm.querySelector('[data-image-preview]');
    const money = (amount) => `EGP ${Number(amount).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;

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
        sellingPrice.textContent = money(total);

        if (calculationNote) {
            calculationNote.textContent = `${grams}g × ${gramCost.toFixed(3)} ج.م/جرام (${filamentName}) × مضاعف ${factor} = ${money(total)}`;
        }
    };

    weight.addEventListener('input', calculate);
    multiplier.addEventListener('change', calculate);
    if (filamentSelect) {
        filamentSelect.addEventListener('change', calculate);
    }

    if (imageInput && imagePreview) {
        imageInput.addEventListener('change', () => {
            const [file] = imageInput.files;
            if (!file) return;
            imagePreview.src = URL.createObjectURL(file);
            imagePreview.hidden = false;
        });
    }

    calculate();
}
