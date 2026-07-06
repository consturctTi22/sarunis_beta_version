@extends('layouts.admin-auth')

@section('title', 'Verifikasi Email '.$portalView['display'])
@section('content')
    <div class="text-center mb-4">
        <h1 class="portal-auth-title mb-2">Verifikasi Email</h1>
        <p class="portal-auth-subtitle">Masukkan email akun {{ strtolower($portalView['display']) }} untuk menerima kode verifikasi.</p>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger portal-auth-alert mb-3" role="alert">
            {{ $errors->first() }}
        </div>
    @endif

    @if (session('status'))
        <div class="alert alert-success portal-auth-alert mb-3" role="alert">
            {{ session('status') }}
        </div>
    @endif

    <form class="portal-auth-form" method="POST" action="{{ route('auth.recovery.send-code') }}">
        @csrf
        <input type="hidden" name="portal" value="{{ $portalKey }}">

        <div class="mb-3">
            <label class="form-label" for="verification-email">Email</label>
            <input
                class="form-control"
                id="verification-email"
                name="email"
                type="email"
                inputmode="email"
                autocomplete="email"
                value="{{ request('email', 'user@sekolah.id') }}"
                placeholder="user@sekolah.id"
                required
            >
        </div>

        <p class="portal-auth-copy mb-3">
            Sudah punya kode?
            <a class="portal-auth-link" href="{{ route('auth.page.verify-code', ['portal' => $portalKey] + request()->only('email')) }}">Isi di sini</a>
        </p>

        <p class="portal-auth-footnote mb-3">
            Demi keamanan, pengiriman kode dibatasi beberapa kali per menit.
        </p>

        <button class="btn btn-primary w-100" type="submit">Kirim Kode</button>
    </form>
@endsection
