@extends('layouts.portal-dashboard')

@section('title', $pageTitle)

@section('content')
    <div class="portal-dashboard-shell" data-dashboard data-dashboard-portal="walikelas">
        @include('dashboard.partials.sidebar', ['menuSections' => $menuSections, 'interactiveSidebar' => false])

        <main class="portal-dashboard-main portal-directory-main">
            <div class="portal-directory-header">
                <div>
                    <span class="portal-hero__badge">{{ $activeAcademicYear }} | {{ ucfirst($activeSemester) }}</span>
                    <h1>Rekap Absensi Kelas</h1>
                    <p>Pantau ringkasan absensi kelas perwalian dan detail catatan siswa.</p>
                </div>
            </div>

            @include('dashboard.partials.homeroom-attendance-recap')
        </main>
    </div>
@endsection
