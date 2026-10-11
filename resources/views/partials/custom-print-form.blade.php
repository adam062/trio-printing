<section class="custom section">
  <div class="custom-grid custom-print-form-grid">
    <form class="quote-card" id="quote-form">
      <div class="quote-top"><span>INSTANT ESTIMATE</span><i id="quote-status">جاهز للتسعير</i></div>
      <label class="dropzone" id="dropzone" for="file-input">
        <input id="file-input" type="file" accept=".stl,.3mf,.zip" hidden />
        <span class="upload-icon">↑</span>
        <b id="file-label">اسحب ملفك هنا أو اضغط للاختيار</b>
        <small>ملفات STL, 3MF أو ZIP · حد أقصى 100 MB</small>
      </label>

      <div class="quote-options">
        <label>نوع الخامة
          <select id="material">
            @forelse($filaments as $filament)
              <option value="{{ $filament->id }}"
                      data-cost="{{ $filament->cost_per_gram }}"
                      data-name="{{ $filament->name }}"
                      data-managed-colors="{{ $filament->hasManagedColorInventory() ? '1' : '0' }}"
                      data-colors="{{ json_encode($filament->colorInventory(), JSON_HEX_APOS | JSON_HEX_QUOT) }}">
                {{ $filament->name }}
              </option>
            @empty
              <option value="" data-cost="0.75" data-name="PLA+" data-colors="[]">لا توجد خامات متاحة حالياً</option>
            @endforelse
          </select>
        </label>
        <label>دقة الطباعة
          <select id="quality">
            <option value="1">Standard — 0.20mm</option>
            <option value="1.25">Fine — 0.12mm (أعلى دقة)</option>
          </select>
        </label>
      </div>

      <div class="color-row">
        <span>لون القطعة</span>
        <div class="quote-colors" id="quote-colors" aria-live="polite"></div>
      </div>

      <div id="quote-customer-fields" style="display:none; padding-top:14px; border-top:1px solid var(--line); margin-top:10px;">
        <p style="font-size:11px; color:var(--lime); margin:0 0 8px;">بيانات التواصل لمراجعة طلبك:</p>
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:8px;">
          <label style="font-size:10px; color:#9aabb0;">اسمك
            <input type="text" id="quote-name" placeholder="الاسم ثلاثي" style="width:100%; background:#102029; border:1px solid #243842; color:#dce6e7; padding:8px; font-size:11px; margin-top:4px;" />
          </label>
          <label style="font-size:10px; color:#9aabb0;">رقم الموبايل
            <input type="tel" id="quote-phone" placeholder="01012345678" style="width:100%; background:#102029; border:1px solid #243842; color:#dce6e7; padding:8px; font-size:11px; margin-top:4px;" />
          </label>
        </div>
        <label style="font-size:10px; color:#9aabb0; display:block; margin-top:8px;">ملاحظات إضافية (اختياري)
          <textarea id="quote-notes" rows="2" placeholder="أي تفاصيل عن الاستخدام أو الكثافة (Infill)" style="width:100%; background:#102029; border:1px solid #243842; color:#dce6e7; padding:8px; font-size:11px; margin-top:4px; resize:none;"></textarea>
        </label>
      </div>

      <div class="estimate">
        <div><span>تقدير السعر المبدئي</span><strong id="quote-price">—</strong></div>
        <div><span>الوقت المتوقع</span><strong id="quote-time">—</strong></div>
        <div style="display:flex; flex-direction:column; gap:6px;">
          <button type="submit" class="button button-primary" id="quote-button" disabled style="padding:10px 12px; font-size:11px; justify-content:center;">
            إرسال الطلب للاستوديو <span>↙</span>
          </button>
          <button type="button" class="button button-quiet" id="quote-whatsapp-button" style="display:none; padding:8px 10px; font-size:10px; justify-content:center; border-color:#25d366; color:#25d366;">
            استفسار عبر واتساب 💬
          </button>
        </div>
      </div>
    </form>
  </div>
</section>
