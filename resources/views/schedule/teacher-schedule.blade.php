@extends('layouts.portal-dashboard')

@section('title', 'Jadwal Mengajar ' . $teacher->name)

@php
    $menuSections = [
        [
            'title' => 'Menu',
            'items' => [
                ['label' => 'Beranda', 'icon' => 'home', 'href' => url('/admin/dashboard'), 'active' => false],
                ['label' => 'Data Siswa', 'icon' => 'students', 'href' => url('/admin/data-siswa'), 'active' => false],
                ['label' => 'Data Guru', 'icon' => 'teacher', 'href' => url('/admin/data-guru'), 'active' => false],
                ['label' => 'Data Kelas', 'icon' => 'class', 'href' => url('/admin/data-kelas'), 'active' => false],
                ['label' => 'Mata Pelajaran', 'icon' => 'subject', 'href' => url('/admin/mata-pelajaran'), 'active' => false],
                ['label' => 'Kalender Akademik', 'icon' => 'calendar', 'href' => url('/admin/kalender-akademik'), 'active' => false],
                ['label' => 'Jadwal Pelajaran', 'icon' => 'schedule', 'href' => url('/admin/schedule/generate'), 'active' => true],
                ['label' => 'Rekap Kehadiran', 'icon' => 'recap', 'href' => url('/admin/rekap-kehadiran'), 'active' => false],
                ['label' => 'Laporan Statistik', 'icon' => 'chart', 'href' => url('/admin/laporan-statistik'), 'active' => false],
            ],
        ],
        [
            'title' => 'Lainnya',
            'items' => [
                ['label' => 'Manajemen Pengguna', 'icon' => 'users', 'href' => url('/admin/manajemen-pengguna'), 'active' => false],
                ['label' => 'Pengaturan', 'icon' => 'settings', 'href' => url('/admin/pengaturan'), 'active' => false],
                ['label' => 'Catatan Siswa', 'icon' => 'note', 'href' => url('/admin/catatan-siswa'), 'active' => false],
            ],
        ],
    ];
@endphp

@section('content')
    <div class="portal-dashboard-shell portal-directory-shell">
        @include('dashboard.partials.sidebar', ['menuSections' => $menuSections])

        <main class="portal-dashboard-main portal-directory-main">
            <div class="portal-directory-stack">
                <section class="portal-panel portal-directory-banner">
                    <div class="portal-directory-banner__bar"></div>
                    <div class="portal-directory-banner__copy">
                        <h1>Jadwal Mengajar {{ $teacher->name }}</h1>
                        <p>Tahun Ajaran {{ $academicYear }} | NIP {{ $teacher->nip ?? '-' }}</p>
                    </div>
                    <div class="portal-directory-banner__count">Guru</div>
                </section>

                <section class="portal-panel p-4 mb-4">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                        <div>
                            <h2 class="fs-5 fw-bold mb-1">Weekly Teacher Schedule</h2>
                            <p class="text-secondary mb-0">Rincian jam mengajar dan beban kerja per minggu.</p>
                        </div>
                        <div>
                            <a href="{{ url('/admin/schedule/generate') }}" class="btn btn-sm btn-secondary">
                                Kembali
                            </a>
                        </div>
                    </div>

                    <div class="row g-3 mt-3">
                        <div class="col-6 col-sm-3">
                            <div class="bg-light p-3 rounded">
                                <small class="text-secondary d-block text-uppercase fw-bold fs-8">Total Jam Ajar</small>
                                <strong class="fs-5">{{ $schedule['stats']['total_hours_per_week'] }} jam/minggu</strong>
                            </div>
                        </div>
                        <div class="col-6 col-sm-3">
                            <div class="bg-light p-3 rounded">
                                <small class="text-secondary d-block text-uppercase fw-bold fs-8">Total Sesi</small>
                                <strong class="fs-5">{{ $schedule['stats']['sessions_per_week'] }} kali</strong>
                            </div>
                        </div>
                        <div class="col-6 col-sm-3">
                            <div class="bg-light p-3 rounded">
                                <small class="text-secondary d-block text-uppercase fw-bold fs-8">Variasi Mapel</small>
                                <strong class="fs-5">{{ $schedule['stats']['unique_subjects'] }} mapel</strong>
                            </div>
                        </div>
                        <div class="col-6 col-sm-3">
                            <div class="bg-light p-3 rounded">
                                <small class="text-secondary d-block text-uppercase fw-bold fs-8">Jumlah Kelas</small>
                                <strong class="fs-5">{{ $schedule['stats']['unique_classes'] }} kelas</strong>
                            </div>
                        </div>
                    </div>
                </section>

                <div class="row row-cols-1 row-cols-md-2 row-cols-xl-5 g-3">
                    @foreach ($schedule['schedule'] as $day => $sessions)
                        <div class="col">
                            <section class="portal-panel h-100 p-3">
                                <div class="border-bottom pb-2 mb-3">
                                    <h3 class="fs-6 fw-bold text-success mb-0">{{ $day }}</h3>
                                    <small class="text-secondary">{{ count($sessions) }} Sesi</small>
                                </div>

                                <div class="d-flex flex-column gap-3">
                                    @forelse ($sessions as $session)
                                        <div class="p-3 bg-light rounded border-start border-success border-4">
                                            <div class="fw-bold fs-7 text-dark mb-1">{{ $session['subject'] }}</div>
                                            <div class="text-secondary fs-8 mb-2">Kelas: {{ $session['class'] }}</div>
                                            <div class="d-flex justify-content-between text-secondary fs-9 border-top pt-2">
                                                <span>⏰ {{ $session['time'] }}</span>
                                                <span class="fw-semibold">📍 {{ $session['room'] }}</span>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="text-center py-5 text-secondary fs-8">
                                            <em>Tidak ada jadwal</em>
                                        </div>
                                    @endforelse
                                </div>
                            </section>
                        </div>
                    @endforeach
                </div>
            </div>
        </main>
    </div>
@endsection
