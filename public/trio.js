// TRIO Storefront & Custom 3D Printing Logic

const loadSavedCart = () => {
    try {
        return JSON.parse(localStorage.getItem('trio_cart') || '[]');
    } catch {
        return [];
    }
};

const saveCart = () => {
    localStorage.setItem('trio_cart', JSON.stringify(state.cart));
};

const state = {
    cart: loadSavedCart(),
    quote: null,
    selectedColor: null,
    uploadedFile: null,
};

const $ = (selector, parent = document) => parent.querySelector(selector);
const $$ = (selector, parent = document) => [...parent.querySelectorAll(selector)];
const money = (amount) => `EGP ${Number(amount).toLocaleString('en-US', { minimumFractionDigits: 0, maximumFractionDigits: 2 })}`;

const navToggle = $('#nav-toggle');
const siteHeader = $('.site-header');
if (navToggle && siteHeader) {
    const closeNav = () => {
        siteHeader.classList.remove('nav-open');
        navToggle.setAttribute('aria-expanded', 'false');
        navToggle.setAttribute('aria-label', 'فتح القائمة');
    };

    navToggle.addEventListener('click', () => {
        const isOpen = siteHeader.classList.toggle('nav-open');
        navToggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        navToggle.setAttribute('aria-label', isOpen ? 'إغلاق القائمة' : 'فتح القائمة');
    });

    $$('.nav-links a').forEach((link) => link.addEventListener('click', closeNav));
}

function cartTotal() {
    return state.cart.reduce((sum, item) => sum + (Number(item.price) || 0) * item.quantity, 0);
}

function renderCart() {
    const count = state.cart.reduce((sum, item) => sum + item.quantity, 0);
    $$('[data-cart-count]').forEach((el) => { el.textContent = count; });
    $$('[data-cart-total], #cart-total').forEach((el) => { el.textContent = money(cartTotal()); });

    if (!state.cart.length) {
        $$('[data-cart-items], #cart-items').forEach((list) => {
            list.innerHTML = '<div class="empty-cart"><span>◌</span><p>لسه مفيش حاجة في السلة.</p></div>';
        });
    } else {
        $$('[data-cart-items], #cart-items').forEach((list) => {
            list.replaceChildren();
            state.cart.forEach((item, index) => {
                const row = document.createElement('article');
                row.className = 'cart-item';

                const thumb = document.createElement('div');
                thumb.className = 'cart-thumb';
                if (item.image) {
                    const image = document.createElement('img');
                    image.src = item.image;
                    image.alt = '';
                    thumb.append(image);
                }

                const details = document.createElement('div');
                details.className = 'cart-item-details';
                const name = document.createElement('h4');
                name.textContent = item.name;
                details.append(name);

                if (item.color) {
                    const color = document.createElement('small');
                    color.className = 'cart-item-color';
                    color.textContent = `اللون: ${item.color}`;
                    details.append(color);
                }

                const price = document.createElement('p');
                price.textContent = `${money(item.price)} · ${item.quantity} ×`;
                details.append(price);

                const actions = document.createElement('div');
                actions.className = 'cart-item-actions';
                const decrease = document.createElement('button');
                decrease.type = 'button';
                decrease.dataset.cartAction = 'decrease';
                decrease.dataset.cartIndex = index;
                decrease.setAttribute('aria-label', 'تقليل الكمية');
                decrease.textContent = '-';

                const quantity = document.createElement('span');
                quantity.textContent = item.quantity;

                const increase = document.createElement('button');
                increase.type = 'button';
                increase.dataset.cartAction = 'increase';
                increase.dataset.cartIndex = index;
                increase.setAttribute('aria-label', 'زيادة الكمية');
                increase.textContent = '+';

                const remove = document.createElement('button');
                remove.type = 'button';
                remove.className = 'cart-item-remove';
                remove.dataset.cartAction = 'remove';
                remove.dataset.cartIndex = index;
                remove.setAttribute('aria-label', 'حذف المنتج');
                remove.textContent = '×';

                actions.append(decrease, quantity, increase, remove);
                row.append(thumb, details, actions);
                list.append(row);
            });
        });
    }

    $$('[data-checkout-link]').forEach((link) => {
        link.setAttribute('aria-disabled', state.cart.length ? 'false' : 'true');
        link.tabIndex = state.cart.length ? 0 : -1;
    });
    const checkoutButton = $('#checkout');
    if (checkoutButton) checkoutButton.disabled = state.cart.length === 0;
}

$$('[data-cart-items], #cart-items').forEach((list) => {
    list.addEventListener('click', (event) => {
        const button = event.target.closest('[data-cart-action]');
        if (!button) return;

        const index = Number(button.dataset.cartIndex);
        const item = state.cart[index];
        if (!item) return;

        if (button.dataset.cartAction === 'remove') {
            state.cart.splice(index, 1);
        } else if (button.dataset.cartAction === 'increase') {
            item.quantity += 1;
        } else if (button.dataset.cartAction === 'decrease') {
            item.quantity = Math.max(1, item.quantity - 1);
        }

        saveCart();
        renderCart();
    });
});

function toast(message) {
    const element = $('#toast');
    if (!element) return;
    element.firstChild.textContent = `${message} `;
    element.classList.add('show');
    window.setTimeout(() => element.classList.remove('show'), 3000);
}

function addItem(id, name, price, color = null, image = null) {
    const item = state.cart.find((cartItem) => cartItem.id === Number(id) && (cartItem.color || null) === (color || null));
    if (item) {
        item.quantity += 1;
        item.image = image || item.image || null;
    } else {
        state.cart.push({
            id: Number(id),
            name,
            color: color || null,
            image: image || null,
            price: Number(price),
            quantity: 1
        });
    }
    saveCart();
    renderCart();
    openCart();
}

// Add to cart buttons listener
$$('.add-button[data-id]').forEach((button) => button.addEventListener('click', (e) => {
    e.stopPropagation();
    addItem(button.dataset.id, button.dataset.name, button.dataset.price, null, button.dataset.image);
}));

function openCart() {
    const drawer = $('#cart-drawer');
    const backdrop = $('#backdrop');
    if (drawer) {
        drawer.classList.add('open');
        drawer.setAttribute('aria-hidden', 'false');
    }
    if (backdrop) backdrop.classList.add('show');
}

$$('[data-open-cart]').forEach((btn) => btn.addEventListener('click', openCart));

$$('[data-checkout-link]').forEach((link) => link.addEventListener('click', (event) => {
    if (!state.cart.length) {
        event.preventDefault();
        toast('أضف قطعة واحدة على الأقل أولاً');
    }
}));

function closeCart() {
    const drawer = $('#cart-drawer');
    const backdrop = $('#backdrop');
    if (drawer) {
        drawer.classList.remove('open');
        drawer.setAttribute('aria-hidden', 'true');
    }
    if (backdrop) backdrop.classList.remove('show');
}

const closeCartBtn = $('#close-cart');
if (closeCartBtn) closeCartBtn.addEventListener('click', closeCart);
const backdropEl = $('#backdrop');
if (backdropEl) backdropEl.addEventListener('click', closeCart);

// إرسال طلب الشراء
const checkoutForm = $('#checkout-form');
if (checkoutForm) {
    checkoutForm.addEventListener('submit', async (event) => {
        event.preventDefault();
        if (!state.cart.length) return toast('أضف قطعة واحدة على الأقل أولاً');

        const button = $('#checkout');
        const form = event.currentTarget;
        const fields = Object.fromEntries(new FormData(form).entries());
        button.disabled = true;
        button.textContent = 'جارٍ إرسال الطلب…';

        try {
            const response = await fetch(window.TRIO_CONFIG.orderUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': window.TRIO_CONFIG.csrf
                },
                body: JSON.stringify({
                    ...fields,
                    cart: state.cart.map(({ id, quantity, color }) => ({ id, quantity, color }))
                }),
            });

            const result = await response.json();
            if (!response.ok) throw new Error(result.message || 'تعذّر إرسال الطلب.');

            state.cart = [];
            saveCart();
            form.reset();
            renderCart();
            const success = $('#checkout-success');
            if (success) {
                const checkoutMain = $('#checkout-main');
                if (checkoutMain) checkoutMain.hidden = true;
                $('#success-order-number').textContent = result.order_number;
                $('#success-total').textContent = money(result.total);
                success.hidden = false;
                success.scrollIntoView({ behavior: 'smooth', block: 'start' });
            } else {
                closeCart();
                toast(`تم استلام طلبك بنجاح (${result.order_number})`);
            }
        } catch (error) {
            toast(error.message || 'تعذّر إرسال الطلب. حاول مرة أخرى.');
        } finally {
            button.disabled = false;
            button.innerHTML = 'تأكيد وإرسال الطلب <span>↙</span>';
        }
    });
}

renderCart();

// قسم تسعير الملفات المخصصة (Custom 3D Printing)
const fileInput = $('#file-input');
const dropzone = $('#dropzone');
const materialSelect = $('#material');
const qualitySelect = $('#quality');
const quoteColors = $('#quote-colors');

function renderQuoteColors() {
    if (!quoteColors || !materialSelect) return;

    const selectedOption = materialSelect.selectedOptions[0];
    let colors = JSON.parse(selectedOption?.dataset.colors || '[]');
    const hasManagedColors = selectedOption?.dataset.managedColors === '1';
    if (!colors.length && !hasManagedColors) {
        colors = [
            { name: 'أخضر ليموني (Lime)', in_stock: true },
            { name: 'أبيض ناصع (White)', in_stock: true },
            { name: 'برتقالي (Orange)', in_stock: true },
            { name: 'أزرق رمادي (Blue)', in_stock: true },
        ];
    }
    quoteColors.replaceChildren();
    state.selectedColor = null;

    if (!colors.length) {
        const hint = document.createElement('span');
        hint.className = 'quote-color-hint';
        hint.textContent = hasManagedColors ? 'نفدت ألوان هذه الخامة' : 'لا توجد ألوان محددة لهذه الخامة';
        quoteColors.append(hint);
        return;
    }

    colors.forEach((color) => {
        const button = document.createElement('button');
        button.type = 'button';
        button.className = 'quote-color-option';
        button.textContent = color.in_stock ? color.name : `${color.name} · نفد`;
        button.disabled = !color.in_stock;
        button.setAttribute('aria-pressed', 'false');

        if (color.in_stock && state.selectedColor === null) {
            state.selectedColor = color.name;
            button.classList.add('active');
            button.setAttribute('aria-pressed', 'true');
        }

        button.addEventListener('click', () => {
            quoteColors.querySelectorAll('.quote-color-option').forEach((option) => {
                option.classList.remove('active');
                option.setAttribute('aria-pressed', 'false');
            });
            button.classList.add('active');
            button.setAttribute('aria-pressed', 'true');
            state.selectedColor = color.name;
            calculateQuote();
        });

        quoteColors.append(button);
    });
}

function calculateQuote() {
    if (!state.uploadedFile) return;

    const file = state.uploadedFile;
    const weight = Math.max(30, Math.round((file.size / 1024 / 1024) * 18 + 40));

    const selectedOption = materialSelect ? materialSelect.selectedOptions[0] : null;
    const hasMaterial = Boolean(selectedOption?.value);
    const gramCost = selectedOption ? (Number(selectedOption.dataset.cost) || 0.75) : 0.75;
    const materialName = selectedOption ? selectedOption.dataset.name : 'PLA+';

    const qualityFactor = Number(qualitySelect ? qualitySelect.value : 1) || 1;
    const multiplier = 4.0;
    const price = Math.round(weight * gramCost * multiplier);
    const hours = Math.max(2, Math.round((weight / 18) * qualityFactor));

    state.quote = {
        weight,
        gramCost,
        materialName,
        filamentId: hasMaterial ? materialSelect.value : null,
        price,
        hours,
        color: state.selectedColor,
        fileName: file.name
    };

    const fileLabel = $('#file-label');
    if (fileLabel) fileLabel.textContent = file.name;
    const quoteStatus = $('#quote-status');
    if (quoteStatus) quoteStatus.textContent = `${weight}g · تقدير ${materialName}`;
    const quotePrice = $('#quote-price');
    if (quotePrice) quotePrice.textContent = money(price);
    const quoteTime = $('#quote-time');
    if (quoteTime) quoteTime.textContent = `${hours}–${hours + 3} ساعات`;

    const customerFields = $('#quote-customer-fields');
    if (customerFields) customerFields.style.display = 'block';

    const quoteBtn = $('#quote-button');
    const hasManagedColors = materialSelect?.selectedOptions[0]?.dataset.managedColors === '1';
    if (quoteBtn) quoteBtn.disabled = !hasMaterial || (hasManagedColors && !state.selectedColor);
    const waButton = $('#quote-whatsapp-button');
    if (waButton) waButton.style.display = !hasMaterial || (hasManagedColors && !state.selectedColor) ? 'none' : 'inline-flex';
}

function handleFileSelection(file) {
    if (!file) return;
    if (!/\.(stl|3mf|zip)$/i.test(file.name)) {
        return toast('الملفات المدعومة هي STL أو 3MF أو ZIP فقط.');
    }
    state.uploadedFile = file;
    calculateQuote();
}

if (fileInput) {
    fileInput.addEventListener('change', (e) => {
        if (e.target.files[0]) handleFileSelection(e.target.files[0]);
    });
}

if (dropzone) {
    ['dragenter', 'dragover'].forEach((eventName) => dropzone.addEventListener(eventName, (e) => {
        e.preventDefault();
        dropzone.classList.add('drag');
    }));
    ['dragleave', 'drop'].forEach((eventName) => dropzone.addEventListener(eventName, (e) => {
        e.preventDefault();
        dropzone.classList.remove('drag');
    }));
    dropzone.addEventListener('drop', (e) => {
        const file = e.dataTransfer.files[0];
        handleFileSelection(file);
    });
}

if (materialSelect) materialSelect.addEventListener('change', () => {
    renderQuoteColors();
    calculateQuote();
});
if (qualitySelect) qualitySelect.addEventListener('change', calculateQuote);
renderQuoteColors();

// إرسال طلب تسعير الملف المخصص للداشبورد
const quoteForm = $('#quote-form');
if (quoteForm) {
    quoteForm.addEventListener('submit', async (event) => {
        event.preventDefault();
        if (!state.uploadedFile || !state.quote || !state.quote.filamentId) {
            return toast('يرجى اختيار ملف 3D أولاً.');
        }

        const nameInput = $('#quote-name');
        const phoneInput = $('#quote-phone');
        const notesInput = $('#quote-notes');

        const name = nameInput ? nameInput.value.trim() : '';
        const phone = phoneInput ? phoneInput.value.trim() : '';
        const notes = notesInput ? notesInput.value.trim() : '';

        if (!name || !phone) {
            toast('يرجى كتابة اسمك ورقم موبايلك لمتابعة الملف.');
            if (!name && nameInput) nameInput.focus();
            else if (!phone && phoneInput) phoneInput.focus();
            return;
        }

        const btn = $('#quote-button');
        btn.disabled = true;
        btn.textContent = 'جارٍ رفع الملف والطلب…';

        const formData = new FormData();
        formData.append('file', state.uploadedFile);
        formData.append('customer_name', name);
        formData.append('phone', phone);
        formData.append('filament_id', state.quote.filamentId);
        formData.append('quality', qualitySelect ? qualitySelect.selectedOptions[0]?.textContent.trim() : 'Standard');
        formData.append('color', state.selectedColor || '');
        formData.append('estimated_weight_grams', state.quote.weight);
        formData.append('original_filename', state.quote.fileName);
        formData.append('notes', notes);

        try {
            const response = await fetch(window.TRIO_CONFIG.quoteUrl, {
                method: 'POST',
                headers: {
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': window.TRIO_CONFIG.csrf
                },
                body: formData,
            });

            const res = await response.json();
            if (!response.ok) throw new Error(res.message || 'تعذّر إرسال الطلب.');

            toast(`تم استلام طلبك (${res.quote_number}) وسيتواصل معك فريق TRIO.`);
            quoteForm?.reset();
            state.uploadedFile = null;
            state.quote = null;
            $('#quote-button').disabled = true;
            $('#quote-price').textContent = '—';
            $('#quote-time').textContent = '—';
            $('#quote-status').textContent = 'تم الإرسال بنجاح';
            $('#file-label').textContent = 'اسحب ملفك هنا أو اضغط للاختيار';
            renderQuoteColors();
            const customerFields = $('#quote-customer-fields');
            if (customerFields) customerFields.style.display = 'none';
        } catch (err) {
            toast(err.message || 'حدث خطأ أثناء رفع الملف.');
        } finally {
            btn.disabled = false;
            btn.innerHTML = 'إرسال الطلب للاستوديو <span>↙</span>';
        }
    });
}

// طلب تسعير مباشر عبر واتساب
const waBtn = $('#quote-whatsapp-button');
if (waBtn) {
    waBtn.addEventListener('click', () => {
        if (!state.quote) return toast('اختر ملف 3D أولاً.');
        const phone = window.TRIO_CONFIG.whatsappPhone || '201282537892';
        const text = encodeURIComponent(
            `أهلاً استوديو TRIO 👋\nحابب أطبع مجسم 3D مخصص:\n` +
            `• اسم الملف: ${state.quote.fileName}\n` +
            `• نوع الخامة: ${state.quote.materialName}\n` +
            `• اللون: ${state.selectedColor || 'غير محدد'}\n` +
            `• الوزن التقديري: ${state.quote.weight} جرام\n` +
            `• السعر التقديري: EGP ${state.quote.price}\n\nجاهز لإرسال الملف للمراجعة والطباعة.`
        );
        window.open(`https://wa.me/${phone}?text=${text}`, '_blank');
    });
}



// تفاعلات الحركة عند التمرير
const observer = new IntersectionObserver((items) => {
    items.forEach((item) => {
        if (item.isIntersecting) item.target.classList.add('show');
    });
}, { threshold: 0.12 });
$$('.reveal,.reveal-delay,.reveal-delay-2').forEach((element) => observer.observe(element));

// معرض صور المنتج - التبديل بين الصور
const mainImage = $('#product-main-image');
const galleryThumbs = $$('.gallery-thumb-btn');

if (mainImage && galleryThumbs.length) {
    galleryThumbs.forEach((thumb) => {
        thumb.addEventListener('click', () => {
            const fullImage = thumb.dataset.full;
            if (fullImage) {
                mainImage.src = fullImage;
                galleryThumbs.forEach((t) => t.classList.remove('active'));
                thumb.classList.add('active');
            }
        });
    });
}

// التهيئة عند فتح الصفحة
renderCart();
