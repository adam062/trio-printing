<footer class="store-footer">
  <div class="footer-brand">
    <div class="footer-mark"><span class="brand-mark"><i></i><i></i><i></i></span>TRIO</div>
    <p>ثلاثة أصدقاء يصنعون أشياء أفضل.</p>
  </div>
  <div class="footer-socials" aria-label="حسابات TRIO">
    <a href="https://wa.me/{{ config('app.whatsapp_phone', '201282537892') }}" target="_blank" rel="noopener noreferrer" aria-label="واتساب">
      <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path class="social-icon-fill" d="M20.5 11.8a8.4 8.4 0 0 1-12.4 7.4L3 20.5l1.4-4.9a8.4 8.4 0 1 1 16.1-3.8ZM8.3 7.7c-.2 0-.5.1-.7.4-.2.2-.9.9-.9 2.1s.9 2.4 1 2.6c.1.2 1.8 2.8 4.4 3.8 2.2.9 2.7.7 3.2.6.5-.1 1.6-.7 1.8-1.3.2-.6.2-1.1.1-1.2-.1-.1-.2-.2-.5-.3l-1.7-.8c-.2-.1-.4-.1-.6.2l-.8 1c-.1.2-.3.2-.5.1-.3-.1-1.1-.4-2-1.2-.7-.6-1.2-1.4-1.4-1.7-.1-.2 0-.4.1-.5l.4-.5c.2-.2.2-.3.3-.5.1-.2 0-.4 0-.5l-.8-1.9c-.2-.5-.4-.5-.6-.5h-.8Z"/></svg>
    </a>
    <a @if(config('app.social_links.instagram')) href="{{ config('app.social_links.instagram') }}" target="_blank" rel="noopener noreferrer" @else aria-disabled="true" @endif aria-label="إنستجرام">
      <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle class="social-icon-dot" cx="17.5" cy="6.5" r="1"/></svg>
    </a>
    @if(config('app.social_links.tiktok'))
      <a href="{{ config('app.social_links.tiktok') }}" target="_blank" rel="noopener noreferrer" aria-label="تيك توك">
        <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
          <path class="social-icon-fill" d="M14.9 3.2c.9 1.7 2.4 2.9 4.2 3.3v3.1a8.3 8.3 0 0 1-4.2-1.2v7.1a5.1 5.1 0 1 1-5.1-5.1c.3 0 .6 0 .9.1v3.1a2 2 0 1 0 1.3 1.9V3.2h3.9Z"/>
        </svg>
      </a>
    @endif
    <a @if(config('app.social_links.facebook')) href="{{ config('app.social_links.facebook') }}" target="_blank" rel="noopener noreferrer" @else aria-disabled="true" @endif aria-label="فيسبوك">
      <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path class="social-icon-fill" d="M13.4 21v-8h2.7l.4-3.1h-3.1v-2c0-.9.3-1.5 1.6-1.5h1.7V3.6c-.3 0-1.3-.1-2.4-.1-2.4 0-4.1 1.5-4.1 4.2v2.3H7.5V13h2.7v8h3.2Z"/></svg>
    </a>
  </div>
  <div class="footer-meta">
    <span>© 2026 TRIO 3D PRINTING</span>
    <a href="{{ route('admin.login') }}">لوحة الإدارة</a>
  </div>
</footer>

<button class="floating-cart" type="button" data-open-cart aria-label="فتح سلة الطلبات">
  <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M6 6h15l-1.5 9h-12L5 3H2" /><circle cx="9" cy="20" r="1.4" /><circle cx="18" cy="20" r="1.4" /></svg>
  <span class="floating-cart-count" data-cart-count>0</span>
</button>
