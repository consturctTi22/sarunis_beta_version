@extends('layouts.portal-dashboard')

@section('title', $pageTitle)

@section('content')
    <div class="portal-dashboard-shell">
        @include('dashboard.partials.sidebar', ['menuSections' => [
            ['title' => 'Menu Utama', 'items' => [
                ['label' => 'Dashboard', 'url' => url('/wakasek-kesiswaan/dashboard'), 'icon' => 'home'],
                ['label' => 'Data Pelanggaran', 'url' => url('/wakasek-kesiswaan/pelanggaran'), 'icon' => 'report']
            ]]
        ], 'interactiveSidebar' => false])

        <main class="portal-dashboard-main">
            <div class="portal-directory-header">
                <div>
                    <h1>Dashboard Wakasek Kesiswaan</h1>
                    <p>Selamat datang, {{ $user->name }}. Kelola dan pantau data pelanggaran siswa di sini.</p>
                </div>
            </div>

            @if (isset($announcements) && $announcements->isNotEmpty())
            <section class="portal-panel mt-4" style="padding: 20px; border-radius: 12px; background: rgba(255, 255, 255, 0.02); border: 1px solid rgba(255, 255, 255, 0.05);">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <span style="color: #ff9f43; font-size: 1.25rem;">📢</span>
                    <h2 class="h5 mb-0 font-semibold" style="color: #fff; font-size: 1.1rem;">Pengumuman Terbaru</h2>
                </div>
                <div class="d-flex flex-column gap-3">
                    @foreach ($announcements as $announcement)
                    <div class="p-3 rounded" style="background: rgba(255, 255, 255, 0.02); border-left: 4px solid #7367f0;">
                        <div class="d-flex justify-content-between align-items-start gap-2 mb-1">
                            <h3 class="h6 mb-0 font-bold" style="color: #fff; font-size: 0.95rem;">{{ $announcement->title }}</h3>
                            <span class="text-muted small" style="font-size: 0.8rem;">{{ $announcement->created_at->diffForHumans() }}</span>
                        </div>
                        <p class="text-muted mb-0 small" style="white-space: pre-line; line-height: 1.5; font-size: 0.875rem;">{{ $announcement->content }}</p>
                        <div class="mt-2 text-muted small d-flex align-items-center gap-1" style="font-size: 0.8rem;">
                            <span>✍️ Oleh: <strong>{{ $announcement->creator?->name ?? 'System' }}</strong></span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </section>
            @endif

            <section class="portal-panel mt-4">
                <h2>Ringkasan</h2>
                <p>Fitur pelanggaran siswa telah aktif. Anda dapat mengelola data pada menu <strong>Data Pelanggaran</strong>.</p>
                <a href="{{ url('/wakasek-kesiswaan/pelanggaran') }}" class="btn btn-primary mt-3">Kelola Pelanggaran</a>
            </section>
        </main>
    </div>
@endsection
