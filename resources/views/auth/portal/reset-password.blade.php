@extends('layouts.admin-auth')

@section('title', 'Lupa Kata Sandi '.$portalView['display'])
@section('content')
    <div class="text-center mb-4">
        <h1 class="portal-auth-title mb-2">Lupa Kata Sandi</h1>
        <p class="portal-auth-subtitle">Atur kata sandi baru untuk portal {{ strtolower($portalView['display']) }}.</p>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger portal-auth-alert mb-3" role="alert">
            {{ $errors->first() }}
        </div>
    @endif

    <form class="portal-auth-form" method="POST" action="{{ route('auth.recovery.reset-password') }}">
        @csrf
        <input type="hidden" name="portal" value="{{ $portalKey }}">
        <input type="hidden" name="token" value="{{ request('token') }}">
        @if (request()->filled('email'))
            <input type="hidden" name="email" value="{{ request('email') }}">
        @endif

        <div class="mb-3">
            <label class="form-label" for="new-password">Kata Sandi Baru</label>
            <input
                class="form-control"
                id="new-password"
                name="password"
                type="password"
                autocomplete="new-password"
                placeholder="Masukkan kata sandi baru"
                required
            >
        </div>

        <div class="mb-3">
            <label class="form-label" for="confirm-password">Konfirmasi Kata Sandi</label>
            <input
                class="form-control"
                id="confirm-password"
                name="password_confirmation"
                type="password"
                autocomplete="new-password"
                placeholder="Ulangi kata sandi baru"
                required
            >
        </div>

        <button class="btn btn-primary w-100 mb-3" type="submit">Kirim</button>

        <p class="portal-auth-footnote mb-0">
            Gunakan minimal 8 karakter serta gabungkan huruf dan angka agar akun lebih aman.
        </p>
    </form>
@endsection
