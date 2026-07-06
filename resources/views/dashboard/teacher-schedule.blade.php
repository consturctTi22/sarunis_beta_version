@extends('layouts.portal-dashboard')

@section('title', $pageTitle)

@section('content')
    <div class="portal-dashboard-shell" data-dashboard data-dashboard-portal="{{ $portalKey ?? 'guru-mapel' }}">
        @include('dashboard.partials.sidebar', ['menuSections' => $menuSections, 'interactiveSidebar' => false])

        <main class="portal-dashboard-main portal-directory-main">
            <div class="portal-directory-header">
                <div>
                    <span class="portal-hero__badge">{{ $activeAcademicYear }} | {{ ucfirst($activeSemester) }}</span>
                    <h1>Jadwal Mengajar</h1>
                    <p>Agenda mengajar hari ini dengan kelas, ruang, dan langkah cepat untuk memulai absensi.</p>
                </div>
            </div>

            @include('dashboard.partials.teacher-schedule')
        </main>
    </div>
@endsection
