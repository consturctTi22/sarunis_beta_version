@extends('layouts.admin-auth')

@php
    $email = (string) request('email', 'user@sekolah.id');
    [$localPart, $domainPart] = array_pad(explode('@', $email, 2), 2, 'sekolah.id');
    $visibleLocal = substr($localPart, 0, min(strlen($localPart), 2));
    $maskedLocal = $visibleLocal.str_repeat('*', max(strlen($localPart) - strlen($visibleLocal), 2));
    $maskedEmail = $maskedLocal.'@'.$domainPart;
@endphp

@section('title', 'Verifikasi Kode '.$portalView['display'])
@section('content')
    <div class="text-center mb-4">
        <h1 class="portal-auth-title mb-2">Verifikasi Email</h1>
        <p class="portal-auth-subtitle">Masukkan kode yang dikirim ke {{ $maskedEmail }}.</p>
    </div>

    @if (request('sent') === '1' || request('resent') === '1')
        <div class="alert alert-success portal-auth-alert mb-3" role="alert">
            Kode verifikasi sudah dikirim.
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger portal-auth-alert mb-3" role="alert">
            {{ $errors->first() }}
        </div>
    @endif

    <form class="portal-auth-form" method="POST" action="{{ route('auth.recovery.verify-code') }}">
        @csrf
        <input type="hidden" name="portal" value="{{ $portalKey }}">
        <input type="hidden" name="email" value="{{ request('email') }}">

        <div class="portal-auth-code mb-3">
            @for ($index = 0; $index < 5; $index++)
                <input
                    class="form-control"
                    name="code[]"
                    type="text"
                    inputmode="numeric"
                    maxlength="1"
                    autocomplete="one-time-code"
                    aria-label="Kode verifikasi digit {{ $index + 1 }}"
                    required
                >
            @endfor
        </div>

        <p class="portal-auth-copy mb-3">
            Ingin mengganti email?
            <a class="portal-auth-link" href="{{ route('auth.page.verify-email', ['portal' => $portalKey] + request()->only('email')) }}">Ubah Email</a>
        </p>

        <div class="portal-auth-actions">
            <button class="btn btn-primary w-100" type="submit">Kirim</button>
            <button class="portal-auth-link text-center border-0 bg-transparent" type="submit" form="resend-code-form">Kirim Ulang</button>
        </div>
    </form>

    <form id="resend-code-form" method="POST" action="{{ route('auth.recovery.send-code') }}" class="d-none">
        @csrf
        <input type="hidden" name="portal" value="{{ $portalKey }}">
        <input type="hidden" name="email" value="{{ request('email') }}">
    </form>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const inputs = Array.from(document.querySelectorAll('.portal-auth-code .form-control'));

            inputs.forEach(function (input, index) {
                input.addEventListener('input', function () {
                    input.value = input.value.replace(/[^0-9a-z]/gi, '').slice(0, 1).toUpperCase();
                    input.dataset.filled = input.value ? 'true' : 'false';

                    if (input.value && inputs[index + 1]) {
                        inputs[index + 1].focus();
                    }
                });

                input.addEventListener('keydown', function (event) {
                    if (event.key === 'Backspace' && !input.value && inputs[index - 1]) {
                        inputs[index - 1].focus();
                    }
                });

                input.addEventListener('paste', function (event) {
                    event.preventDefault();

                    const pastedValue = (event.clipboardData || window.clipboardData)
                        .getData('text')
                        .replace(/[^0-9a-z]/gi, '')
                        .toUpperCase()
                        .slice(0, inputs.length);

                    pastedValue.split('').forEach(function (character, charIndex) {
                        if (inputs[charIndex]) {
                            inputs[charIndex].value = character;
                            inputs[charIndex].dataset.filled = 'true';
                        }
                    });

                    const nextInput = inputs[Math.min(pastedValue.length, inputs.length - 1)];

                    if (nextInput) {
                        nextInput.focus();
                    }
                });
            });
        });
    </script>
@endpush
