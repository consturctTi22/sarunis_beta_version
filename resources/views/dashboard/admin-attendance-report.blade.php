@extends('layouts.portal-dashboard')

@section('title', $pageTitle)

@section('content')
    <div class="portal-dashboard-shell" data-dashboard data-dashboard-portal="admin">
        @include('dashboard.partials.sidebar', ['menuSections' => $menuSections, 'interactiveSidebar' => false])

        <main class="portal-dashboard-main portal-directory-main">
            <div class="portal-directory-header">
                <div>
                    <span class="portal-hero__badge">{{ $activeAcademicYear }} | {{ ucfirst($activeSemester) }}</span>
                    <h1>Laporan Statistik</h1>
                    <p>Analisis tren absensi, siswa perlu perhatian, kelas rendah hadir, dan rekap mapel/guru.</p>
                </div>
            </div>

            @include('dashboard.partials.admin-attendance-report')
        </main>
    </div>
@endsection
