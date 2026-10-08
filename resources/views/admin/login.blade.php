@extends('admin.layout')

@section('title', 'TRIO · Admin access')

@section('content')
<main class="login-page">
    <a class="admin-brand" href="{{ route('storefront') }}"><span class="brand-bars"><i></i><i></i><i></i></span>TRIO</a>
    <section class="login-card">
        <p class="eyebrow">PRIVATE STUDIO ACCESS</p>
        <h1>لوحة تشغيل<br/><em>TRIO</em></h1>
        <p class="login-copy">هذه المساحة خاصة بفريق TRIO فقط.</p>
        @if($errors->any())<div class="alert error">{{ $errors->first() }}</div>@endif
        <form method="POST" action="{{ route('admin.login.store') }}" class="admin-form">
            @csrf
            <label>البريد الإلكتروني<input type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="email" /></label>
            <label>كلمة المرور<input type="password" name="password" required autocomplete="current-password" /></label>
            <button class="lime-button" type="submit">دخول للوحة <span>↙</span></button>
        </form>
    </section>
</main>
@endsection
