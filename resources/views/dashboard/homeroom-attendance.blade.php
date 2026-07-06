@extends('layouts.portal-dashboard')

@section('title', $pageTitle)

@section('content')
    <div class="portal-dashboard-shell" data-dashboard data-dashboard-portal="{{ $portalKey ?? 'walikelas' }}">
        @include('dashboard.partials.sidebar', ['menuSections' => $menuSections, 'interactiveSidebar' => false])

        <main class="portal-dashboard-main portal-directory-main portal-teacher-attendance-page">
            <div class="portal-directory-header portal-teacher-attendance-hero">
                <div>
                    <span class="portal-hero__badge">{{ $activeAcademicYear }} | {{ ucfirst($activeSemester) }}</span>
                    <h1>Absensi Kelas</h1>
                    <p>Pilih kelas perwalian, tandai kehadiran siswa, lalu simpan absensi kelas.</p>
                </div>
                <div class="portal-teacher-attendance-hero__meta">
                    <span>{{ count($classes ?? []) }} kelas perwalian</span>
                    <strong>{{ count($homeroomStudents ?? []) }} siswa</strong>
                </div>
            </div>

            @include('dashboard.partials.homeroom-attendance-form')
        </main>
    </div>
@endsection

@push('scripts')
    @include('dashboard.partials.homeroom-attendance-script')
@endpush
