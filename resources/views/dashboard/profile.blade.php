@extends('layouts.portal-dashboard')

@section('title', $pageTitle)

@section('content')
    <div class="portal-dashboard-shell" data-dashboard data-dashboard-portal="{{ $portalKey }}">
        @include('dashboard.partials.sidebar', ['menuSections' => $menuSections, 'interactiveSidebar' => true])

        <main class="portal-dashboard-main portal-directory-main">
            <div class="portal-directory-header">
                <div>
                    <h1>{{ $pageTitle }}</h1>
                    <p>Kelola informasi akun dan kata sandi Anda.</p>
                </div>
            </div>

            @if (session('status') === 'profil-diperbarui')
                <div class="alert alert-success mt-4">
                    Profil Anda berhasil diperbarui.
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger mt-4">
                    Terdapat kesalahan pada input Anda. Silakan periksa kembali formulir di bawah.
                </div>
            @endif

            <section class="portal-panel mt-4" style="max-width: 800px;">
                <div class="portal-section-heading">
                    <div>
                        <h2>Informasi Akun</h2>
                        <p>Perbarui informasi detail diri dan kontak Anda di bawah ini.</p>
                    </div>
                </div>

                <form method="post" action="{{ route('profile.update') }}" class="portal-form">
                    @csrf
                    @method('patch')

                    <div class="mb-4">
                        <label for="name" class="form-label">Nama Lengkap</label>
                        <input id="name" name="name" type="text" class="form-control" value="{{ old('name', $profile->name ?? $user->name) }}" readonly disabled>
                        <small class="text-muted">Nama tidak dapat diubah secara mandiri. Hubungi administrator jika ada kesalahan.</small>
                    </div>

                    <div class="mb-4">
                        <label for="email" class="form-label">Email</label>
                        <input id="email" name="email" type="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required autocomplete="username">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="phone" class="form-label">Nomor Telepon</label>
                        <input id="phone" name="phone" type="text" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $profile->phone ?? '') }}">
                        @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="address" class="form-label">Alamat Lengkap</label>
                        <textarea id="address" name="address" class="form-control @error('address') is-invalid @enderror" rows="3">{{ old('address', $profile->address ?? '') }}</textarea>
                        @error('address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="portal-section-heading mt-5">
                        <div>
                            <h2>Ganti Kata Sandi</h2>
                            <p>Pastikan akun Anda menggunakan kata sandi yang panjang dan acak untuk tetap aman.</p>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="current_password" class="form-label">Kata Sandi Saat Ini</label>
                        <input id="current_password" name="current_password" type="password" class="form-control @error('current_password') is-invalid @enderror" autocomplete="current-password">
                        @error('current_password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="password" class="form-label">Kata Sandi Baru</label>
                        <input id="password" name="password" type="password" class="form-control @error('password') is-invalid @enderror" autocomplete="new-password">
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="password_confirmation" class="form-label">Konfirmasi Kata Sandi Baru</label>
                        <input id="password_confirmation" name="password_confirmation" type="password" class="form-control @error('password_confirmation') is-invalid @enderror" autocomplete="new-password">
                        @error('password_confirmation')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mt-5">
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </form>
            </section>
        </main>
    </div>
@endsection
