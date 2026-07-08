@extends('layouts.portal-dashboard')

@section('title', $pageTitle)

@section('content')
    <div class="portal-dashboard-shell" data-dashboard data-dashboard-portal="{{ $portalKey }}">
        @include('dashboard.partials.sidebar', ['menuSections' => $menuSections, 'interactiveSidebar' => true])

        <main class="portal-dashboard-main portal-directory-main">
            <div class="portal-directory-header" style="margin-bottom: 24px;">
                <div class="d-flex align-items-center gap-3">
                    <a href="{{ url('/siswa/daftar-hadir') }}" class="btn btn-light btn-sm" style="height: 36px; width: 36px; display: flex; align-items: center; justify-content: center; border-radius: 8px;">
                        @include('dashboard.partials.icon', ['name' => 'chevron-left', 'size' => 16])
                    </a>
                    <div>
                        <h1>Detail Daftar Hadir</h1>
                        <p>Riwayat absensi untuk mata pelajaran <strong>{{ $assignment->subject?->name ?? '-' }}</strong> yang diajarkan oleh <strong>{{ $assignment->teacher?->name ?? '-' }}</strong>.</p>
                    </div>
                </div>
            </div>

            <section class="portal-panel">
                <div class="portal-section-heading">
                    <div>
                        <h2>Riwayat Kehadiran</h2>
                        <p>Daftar lengkap kehadiran per tanggal pembelajaran.</p>
                    </div>
                </div>

                <div class="table-responsive mt-3">
                    <table class="table portal-table mb-0">
                        <thead>
                            <tr>
                                <th>Tanggal Absen</th>
                                <th>Status Kehadiran</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($attendances as $attendance)
                            <tr data-search-item>
                                <td>{{ $attendance->attendance_date?->translatedFormat('l, d F Y') ?? '-' }}</td>
                                <td>
                                    @php
                                        $statusClass = match(strtolower($attendance->status)) {
                                            'hadir' => 'success',
                                            'sakit' => 'primary',
                                            'izin' => 'warning',
                                            'alpha' => 'danger',
                                            default => 'secondary'
                                        };
                                        $statusLabel = ucfirst($attendance->status);
                                    @endphp
                                    <span class="portal-badge is-{{ $statusClass }}">{{ $statusLabel }}</span>
                                </td>
                                <td>{{ $attendance->notes ?? '-' }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted py-5">
                                    Belum ada catatan absensi untuk mata pelajaran ini.
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
