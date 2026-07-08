@extends('layouts.portal-dashboard')

@section('title', $pageTitle)

@section('content')
    <div class="portal-dashboard-shell" data-dashboard data-dashboard-portal="{{ $portalKey }}">
        @include('dashboard.partials.sidebar', ['menuSections' => $menuSections, 'interactiveSidebar' => true])

        <main class="portal-dashboard-main portal-directory-main">
            <div class="portal-directory-header">
                <div>
                    <h1>{{ $pageTitle }}</h1>
                    <p>Rekapitulasi daftar hadir berdasarkan mata pelajaran.</p>
                </div>
            </div>

            <section class="portal-panel mt-4">
                <div class="portal-section-heading">
                    <div>
                        <h2>Daftar Hadir Per Mata Pelajaran</h2>
                        <p>Total kehadiran, sakit, izin, dan alpha pada masing-masing mapel.</p>
                    </div>
                </div>

                <div class="table-responsive mt-3">
                    <table class="table portal-table mb-0">
                        <thead>
                            <tr>
                                <th>Mata Pelajaran</th>
                                <th>Guru Mapel</th>
                                <th class="text-center">Alpha</th>
                                <th class="text-center">Izin</th>
                                <th class="text-center">Sakit</th>
                                <th>Persentase Kehadiran</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($recapRows as $row)
                            <tr data-search-item>
                                <td><strong>{{ $row['subject_name'] }}</strong></td>
                                <td>{{ $row['teacher_name'] }}</td>
                                <td class="text-center"><span class="portal-badge is-danger">{{ $row['alpha'] }}</span></td>
                                <td class="text-center"><span class="portal-badge is-warning">{{ $row['izin'] }}</span></td>
                                <td class="text-center"><span class="portal-badge is-primary">{{ $row['sakit'] }}</span></td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="progress portal-progress flex-grow-1" style="height: 6px; margin: 0;">
                                            <div class="progress-bar bg-success" style="width: {{ $row['presentase'] }}%"></div>
                                        </div>
                                        <span class="small font-medium" style="min-width: 40px;">{{ $row['presentase'] }}%</span>
                                    </div>
                                </td>
                                <td>
                                    <a href="{{ url('/siswa/daftar-hadir/' . $row['assignment_id']) }}" class="btn btn-sm btn-outline-primary" style="padding: 4px 10px; font-size: 0.8rem; border-radius: 4px;">
                                        Detail
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-5">
                                    Belum ada data absensi untuk ditampilkan.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

        </main>
    </div>
@endsection
