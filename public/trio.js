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
    selectedColor: 'أخضر ليموني (Lime)',
    uploadedFile: null,
};

const $ = (selector, parent = document) => parent.querySelector(selector);
const $$ = (selector, parent = document) => [...parent.querySelectorAll(selector)];
const money = (amount) => `EGP ${Number(amount).toLocaleString('en-US', { minimumFractionDigits: 0, maximumFractionDigits: 2 })}`;

function cartTotal() {
    return state.cart.reduce((sum, item) => sum + (Number(item.price) || 0) * item.quantity, 0);
}

function renderCart() {
    const list = $('#cart-items');
    if (!list) return;
    const count = state.cart.reduce((sum, item) => sum + item.quantity, 0);
    const cartCountEl = $('#cart-count');
    if (cartCountEl) cartCountEl.textContent = count;
    const cartTotalEl = $('#cart-total');
    if (cartTotalEl) cartTotalEl.textContent = money(cartTotal());

    if (!state.cart.length) {
        list.innerHTML = '<div class="empty-cart"><span>◌</span><p>لسه مفيش حاجة في السلة.</p></div>';
        return;
    }

    list.innerHTML = state.cart.map((item, index) => `
        <div class="cart-item">
          <div class="cart-thumb"></div>
          <div>
            <h4>${item.name}</h4>
            ${item.color ? `<small style="color:var(--lime); display:block; font-size:10px; margin-top:2px;">اللون: ${item.color}</small>` : ''}
            <p>${money(item.price)} × ${item.quantity}</p>
          </div>
          <button data-remove="${index}" aria-label="حذف">×</button>
        </div>
    `).join('');

    $$('[data-remove]').forEach((button) => button.addEventListener('click', () => {
        state.cart.splice(Number(button.dataset.remove), 1);
        saveCart();
        renderCart();
    }));
}

function toast(message) {
    const element = $('#toast');
    if (!element) return;
    element.firstChild.textContent = `${message} `;
    element.classList.add('show');
    window.setTimeout(() => element.classList.remove('show'), 3000);
}

function addItem(id, name, price, color = null) {
    const item = state.cart.find((cartItem) => cartItem.id === Number(id) && (cartItem.color || null) === (color || null));
    if (item) {
        item.quantity += 1;
    } else {
        state.cart.push({
            id: Number(id),
            name,
            color: color || null,
            price: Number(price),
            quantity: 1
        });
    }
    saveCart();
    renderCart();
    toast(color ? `تمت إضافة (${name} - ${color}) للسلة` : `تمت إضافة (${name}) للسلة`);
}

// Add to cart buttons listener
$$('.add-button').forEach((button) => button.addEventListener('click', (e) => {
    e.stopPropagation();
    addItem(button.dataset.id, button.dataset.name, button.dataset.price);
}));

const openCartBtn = $('#open-cart');
if (openCartBtn) {
    openCartBtn.addEventListener('click', () => {
        const drawer = $('#cart-drawer');
        const backdrop = $('#backdrop');
        if (drawer) {
            drawer.classList.add('open');
            drawer.setAttribute('aria-hidden', 'false');
        }
        if (backdrop) backdrop.classList.add('show');
    });
}

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
        const fields = Object.fromEntries(new FormData(event.currentTarget).entries());
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
            event.currentTarget.reset();
            renderCart();
            closeCart();
            toast(`تم استلام طلبك بنجاح (${result.order_number})`);
        } catch (error) {
            toast(error.message || 'تعذّر إرسال الطلب. حاول مرة أخرى.');
        } finally {
            button.disabled = false;
            button.innerHTML = 'تأكيد وإرسال الطلب <span>↙</span>';
        }
    });
}

// قسم تسعير الملفات المخصصة (Custom 3D Printing)
const fileInput = $('#file-input');
const dropzone = $('#dropzone');
const materialSelect = $('#material');
const qualitySelect = $('#quality');

function calculateQuote() {
    if (!state.uploadedFile) return;

    const file = state.uploadedFile;
    const weight = Math.max(30, Math.round((file.size / 1024 / 1024) * 18 + 40));

    const selectedOption = materialSelect ? materialSelect.selectedOptions[0] : null;
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
        filamentId: materialSelect ? materialSelect.value : null,
        price,
        hours,
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
    if (quoteBtn) quoteBtn.disabled = false;
    const waButton = $('#quote-whatsapp-button');
    if (waButton) waButton.style.display = 'inline-flex';
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

if (materialSelect) materialSelect.addEventListener('change', calculateQuote);
if (qualitySelect) qualitySelect.addEventListener('change', calculateQuote);

// اختيار اللون في فورم الملفات
$$('.swatch').forEach((swatch) => swatch.addEventListener('click', () => {
    $$('.swatch').forEach((item) => item.classList.remove('active'));
    swatch.classList.add('active');
    state.selectedColor = swatch.dataset.color || 'Lime';
}));

// إرسال طلب تسعير الملف المخصص للداشبورد
const quoteForm = $('#quote-form');
if (quoteForm) {
    quoteForm.addEventListener('submit', async (event) => {
        event.preventDefault();
        if (!state.uploadedFile || !state.quote) {
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
        formData.append('color', state.selectedColor);
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
            quoteForm.reset();
            state.uploadedFile = null;
            state.quote = null;
            $('#quote-button').disabled = true;
            $('#quote-price').textContent = '—';
            $('#quote-time').textContent = '—';
            $('#quote-status').textContent = 'تم الإرسال بنجاح';
            $('#file-label').textContent = 'اسحب ملفك هنا أو اضغط للاختيار';
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
            `• اللون: ${state.selectedColor}\n` +
            `• الوزن التقديري: ${state.quote.weight} جرام\n` +
            `• السعر التقديري: EGP ${state.quote.price}\n\nجاهز لإرسال الملف للمراجعة والطباعة.`
        );
        window.open(`https://wa.me/${phone}?text=${text}`, '_blank');
    });
}

// ثيم الإضاءة
const themeBtn = $('.theme-button');
if (themeBtn) {
    themeBtn.addEventListener('click', () => document.body.classList.toggle('light'));
}

// تفاعلات الحركة عند التمرير
const observer = new IntersectionObserver((items) => {
    items.forEach((item) => {
        if (item.isIntersecting) item.target.classList.add('show');
    });
}, { threshold: 0.12 });
$$('.reveal,.reveal-delay,.reveal-delay-2').forEach((element) => observer.observe(element));

// التهيئة عند فتح الصفحة
renderCart();
