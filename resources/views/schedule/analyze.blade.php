@extends('layouts.portal-dashboard')

@section('title', 'Analisis & Audit Jadwal')

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
                        <h1>Analisis & Audit Jadwal</h1>
                        <p>Tahun Ajaran {{ $academicYear }} | Temukan potensi konflik dan optimalkan pembagian jam mengajar.</p>
                    </div>
                    <div class="portal-directory-banner__count">Audit</div>
                </section>

                <section class="portal-panel p-4 mb-4">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                        <div>
                            <h2 class="fs-5 fw-bold mb-1">Diagnostic Report Overview</h2>
                            <p class="text-secondary mb-0">Rangkuman audit jadwal pelajaran di seluruh kelas.</p>
                        </div>
                        <div>
                            <a href="{{ url('/admin/schedule/generate') }}" class="btn btn-sm btn-secondary">
                                Kembali
                            </a>
                        </div>
                    </div>

                    <div class="row g-3 mt-3">
                        <div class="col-6 col-sm-3 col-md-2">
                            <div class="bg-light p-3 rounded text-center">
                                <small class="text-secondary d-block text-uppercase fw-bold fs-8">Total Guru</small>
                                <strong class="fs-5">{{ $analysis['summary']['total_teachers'] }}</strong>
                            </div>
                        </div>
                        <div class="col-6 col-sm-3 col-md-2">
                            <div class="bg-light p-3 rounded text-center">
                                <small class="text-secondary d-block text-uppercase fw-bold fs-8">Total Kelas</small>
                                <strong class="fs-5">{{ $analysis['summary']['total_classes'] }}</strong>
                            </div>
                        </div>
                        <div class="col-6 col-sm-3 col-md-2">
                            <div class="bg-light p-3 rounded text-center">
                                <small class="text-secondary d-block text-uppercase fw-bold fs-8">Total Jadwal</small>
                                <strong class="fs-5">{{ $analysis['summary']['total_assignments'] }}</strong>
                            </div>
                        </div>
                        <div class="col-6 col-sm-3 col-md-2">
                            <div class="bg-light p-3 rounded text-center">
                                <small class="text-secondary d-block text-uppercase fw-bold fs-8">Total Mapel</small>
                                <strong class="fs-5">{{ $analysis['summary']['total_subjects'] }}</strong>
                            </div>
                        </div>
                        <div class="col-6 col-sm-3 col-md-2">
                            <div class="bg-light p-3 rounded text-center">
                                <small class="text-secondary d-block text-uppercase fw-bold fs-8">Bentrok Guru</small>
                                <strong class="fs-5 {{ $analysis['conflicts']['teacher_conflicts'] > 0 ? 'text-danger' : 'text-success' }}">
                                    {{ $analysis['conflicts']['teacher_conflicts'] }}
                                </strong>
                            </div>
                        </div>
                        <div class="col-6 col-sm-3 col-md-2">
                            <div class="bg-light p-3 rounded text-center">
                                <small class="text-secondary d-block text-uppercase fw-bold fs-8">Bentrok Ruang</small>
                                <strong class="fs-5 {{ $analysis['conflicts']['room_conflicts'] > 0 ? 'text-danger' : 'text-success' }}">
                                    {{ $analysis['conflicts']['room_conflicts'] }}
                                </strong>
                            </div>
                        </div>
                    </div>
                </section>

                <div class="row g-4">
                    <div class="col-lg-6">
                        <section class="portal-panel p-4 h-100">
                            <h2 class="fs-5 fw-bold mb-3">Rekomendasi Optimasi</h2>
                            
                            @forelse ($analysis['recommendations'] as $rec)
                                @php
                                    $alertClass = 'alert-info';
                                    if ($rec['severity'] === 'critical') $alertClass = 'alert-danger';
                                    elseif ($rec['severity'] === 'high') $alertClass = 'alert-warning';
                                @endphp
                                <div class="alert {{ $alertClass }} p-3 mb-3 rounded">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <strong class="fs-6">{{ $rec['title'] }}</strong>
                                        <span class="badge bg-dark text-white text-uppercase" style="font-size: 0.65rem;">{{ $rec['severity'] }}</span>
                                    </div>
                                    <p class="mb-2 fs-7">{{ $rec['description'] }}</p>
                                    
                                    @if (!empty($rec['details']))
                                        <div class="bg-white bg-opacity-50 p-2 rounded mb-2 fs-8">
                                            @if ($rec['type'] === 'overloaded_teachers')
                                                <ul class="mb-0 ps-3">
                                                    @foreach ($rec['details'] as $det)
                                                        <li>{{ $det['teacher'] }}: {{ $det['hours'] }} jam mengajar ({{ $det['sessions'] }} sesi)</li>
                                                    @endforeach
                                                </ul>
                                            @elseif ($rec['type'] === 'imbalanced_schedule')
                                                <ul class="mb-0 ps-3">
                                                    @foreach ($rec['details'] as $det)
                                                        <li>Kelas {{ $det['class'] }}: deviasi beban harian mencapai {{ $det['max_deviation'] }} jam</li>
                                                    @endforeach
                                                </ul>
                                            @elseif ($rec['type'] === 'unscheduled_teachers')
                                                <div class="fw-semibold">Nama guru:</div>
                                                <div class="text-wrap">{{ implode(', ', $rec['details']) }}</div>
                                            @endif
                                        </div>
                                    @endif

                                    <div class="border-top pt-2 mt-2 fs-8 fw-semibold">
                                        💡 Tindakan: {{ $rec['action'] }}
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-5 text-secondary">
                                    <span class="fs-1 d-block mb-2">🎉</span>
                                    <p class="mb-0 fw-semibold text-success">Jadwal Sempurna! Tidak ada rekomendasi perbaikan.</p>
                                </div>
                            @endforelse
                        </section>
                    </div>

                    <div class="col-lg-6">
                        <section class="portal-panel p-4 h-100">
                            <h2 class="fs-5 fw-bold mb-3">Beban Kerja Guru</h2>
                            <div class="table-responsive" style="max-height: 500px;">
                                <table class="table portal-table portal-directory-table mb-0 fs-7">
                                    <thead>
                                        <tr>
                                            <th>Nama Guru</th>
                                            <th>Jam/Minggu</th>
                                            <th>Sesi</th>
                                            <th>Mapel</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($analysis['workload_analysis'] as $w)
                                            <tr>
                                                <td>
                                                    <div class="fw-semibold">{{ $w['teacher_name'] }}</div>
                                                    <small class="text-secondary">{{ $w['unique_classes'] }} kelas</small>
                                                </td>
                                                <td>{{ $w['total_hours_per_week'] }} jam</td>
                                                <td>{{ $w['sessions_per_week'] }} kali</td>
                                                <td>{{ $w['unique_subjects'] }}</td>
                                                <td>
                                                    @php
                                                        $badgeClass = 'bg-success';
                                                        if ($w['workload_status'] === 'Sangat Tinggi' || $w['is_overloaded']) $badgeClass = 'bg-danger';
                                                        elseif ($w['workload_status'] === 'Tinggi') $badgeClass = 'bg-warning text-dark';
                                                        elseif ($w['workload_status'] === 'Rendah') $badgeClass = 'bg-secondary';
                                                    @endphp
                                                    <span class="badge {{ $badgeClass }}">{{ $w['workload_status'] }}</span>
                                                    @if ($w['is_overloaded'])
                                                        <span class="badge bg-danger" title="Beban mengajar melebihi batas 25 jam">OVERLOAD</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </section>
                    </div>
                </div>
            </div>
        </main>
    </div>
@endsection
