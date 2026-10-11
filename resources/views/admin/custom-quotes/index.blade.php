@extends('admin.layout')

@section('title', 'TRIO · طلبات الطباعة الخاصة والتصاميم')

@section('content')
<div class="admin-shell">
    @include('admin.sidebar')

    <main class="admin-main">
        <header class="admin-topbar">
            <div>
                <p class="eyebrow">CUSTOM 3D PRINTS</p>
                <h1>طلبات الطباعة الخاصة وملفات العملاء <span>↗</span></h1>
            </div>
            <a class="store-link" href="{{ route('admin.dashboard') }}">← عودة لنظرة عامة</a>
        </header>

        @if(session('success'))
            <div class="alert success">{{ session('success') }}</div>
        @endif

        <!-- شريط الفلاتر والبحث -->
        <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px; margin-bottom:20px;">
            <div style="display:flex; gap:6px; flex-wrap:wrap;">
                @php
                    $statuses = [
                        'all' => 'الكل',
                        'new' => 'جديد',
                        'contacted' => 'تم التواصل',
                        'approved' => 'معتمد للطباعة',
                        'cancelled' => 'ملغي'
                    ];
                @endphp
                @foreach($statuses as $key => $label)
                    <a href="{{ route('admin.custom_quotes.index', ['status' => $key === 'all' ? null : $key, 'search' => $currentSearch]) }}" 
                       style="padding:6px 12px; border-radius:4px; font-size:11px; text-decoration:none; border:1px solid #273e4b; {{ ($currentStatus === $key || ($key === 'all' && empty($currentStatus))) ? 'background:var(--lime); color:#071019; font-weight:bold;' : 'background:#09151e; color:#dbe6e8;' }}">
                        {{ $label }} ({{ $statusCounts[$key] ?? 0 }})
                    </a>
                @endforeach
            </div>

            <form method="GET" action="{{ route('admin.custom_quotes.index') }}" style="display:flex; gap:8px;">
                @if($currentStatus)
                    <input type="hidden" name="status" value="{{ $currentStatus }}">
                @endif
                <input name="search" value="{{ $currentSearch }}" placeholder="بحث بالاسم، الهاتف، أو اسم الملف..." style="background:#09151e; border:1px solid #273e4b; color:#fff; padding:7px 12px; border-radius:4px; font-size:11px; width:220px;" />
                <button type="submit" style="background:#1b2f3a; color:var(--lime); border:1px solid #365565; border-radius:4px; padding:0 12px; font-size:11px; cursor:pointer;">بحث</button>
            </form>
        </div>

        <div style="display:grid; gap:14px;">
            @forelse($quotes as $quote)
                <div class="quote-admin-card" style="background:#0a1620; border:1px solid var(--line); border-radius:8px; padding:18px; display:grid; grid-template-columns: 1.2fr 1.3fr auto; gap:20px; align-items:center;">
                    <div>
                        <span style="font-family:var(--mono); color:var(--lime); font-size:13px; font-weight:bold;">{{ $quote->quote_number }}</span>
                        <h3 style="margin:4px 0 2px 0; font-size:16px;">{{ $quote->customer_name }}</h3>
                        <a href="tel:{{ $quote->phone }}" style="color:var(--muted); font-size:11px; font-family:var(--mono); text-decoration:none;">📞 {{ $quote->phone }}</a>
                        @if($quote->email)
                            <small style="color:var(--muted); display:block; font-size:10px;">✉ {{ $quote->email }}</small>
                        @endif
                        <small style="display:block; color:var(--muted); font-size:10px; margin-top:6px;">
                            تاريخ الرفع: {{ $quote->created_at->format('Y-m-d · H:i') }}
                        </small>
                    </div>

                    <div style="background:#04090e; padding:12px; border-radius:6px; border:1px solid rgba(255,255,255,0.05);">
                        <div style="display:flex; justify-content:space-between; margin-bottom:6px;">
                            <span style="color:#edf2f1; font-weight:bold; font-size:12px;">ملف: {{ $quote->original_filename }}</span>
                            <strong style="color:var(--lime); font-family:var(--mono); font-size:13px;">EGP {{ number_format($quote->estimated_price, 2) }}</strong>
                        </div>

                        <p style="margin:0; font-size:11px; color:var(--muted);">
                            الخامة: <strong style="color:#fff;">{{ $quote->filament_name }}</strong> · 
                            اللون: <strong style="color:#fff;">{{ $quote->color }}</strong> · 
                            الدقة: <strong style="color:#fff;">{{ $quote->quality }}</strong> · 
                            الوزن التقديري: <strong style="color:#fff;">{{ $quote->estimated_weight_grams }}g</strong>
                        </p>

                        @if($quote->notes)
                            <div style="margin-top:8px; font-size:11px; color:#c6d6d8; border-top:1px dashed #1a2d38; padding-top:6px;">
                                ملاحظات العميل: {{ $quote->notes }}
                            </div>
                        @endif
                    </div>

                    <div style="display:flex; flex-direction:column; gap:8px; align-items:flex-end;">
                        @if($quote->file_path)
                            <a href="{{ route('admin.custom_quotes.download', $quote) }}" class="lime-button" style="padding:7px 14px; font-size:11px; height:auto; text-decoration:none;">
                                تحميل ملف 3D ↓
                            </a>
                        @endif

                        @php
                            $waPhoneQ = preg_replace('/[^0-9]/', '', $quote->phone);
                            if(str_starts_with($waPhoneQ, '0')) { $waPhoneQ = '20'.substr($waPhoneQ, 1); }
                            $waQuoteText = urlencode("أهلاً {$quote->customer_name}، نتواصل معك من استوديو TRIO للطباعة ثلاثية الأبعاد بخصوص ملفك ({$quote->original_filename}) لمراجعة التفاصيل وتأكيد موعد الطباعة والتسليم.");
                        @endphp
                        <a href="https://wa.me/{{ $waPhoneQ }}?text={{ $waQuoteText }}" target="_blank" style="background:#25d366; color:#071019; padding:6px 12px; border-radius:4px; text-decoration:none; font-size:11px; font-weight:bold;">
                            تواصل واتساب ↗
                        </a>

                        <form method="POST" action="{{ route('admin.custom_quotes.status', $quote) }}">
                            @csrf
                            @method('PATCH')
                            <select name="status" onchange="this.form.submit()" style="background:#04090e; border:1px solid #334e5c; color:#dce7e5; border-radius:4px; padding:6px 8px; font-size:10px;">
                                <option value="new" @selected($quote->status === 'new')>جديد</option>
                                <option value="contacted" @selected($quote->status === 'contacted')>تم التواصل</option>
                                <option value="approved" @selected($quote->status === 'approved')>معتمد للطباعة</option>
                                <option value="cancelled" @selected($quote->status === 'cancelled')>ملغي</option>
                            </select>
                        </form>
                    </div>
                </div>
            @empty
                <div class="empty-state">لا توجد طلبات تصاميم مخصصة حالياً.</div>
            @endforelse

            <div style="margin-top:15px;">
                {{ $quotes->links() }}
            </div>
        </div>
    </main>
</div>
@endsection
