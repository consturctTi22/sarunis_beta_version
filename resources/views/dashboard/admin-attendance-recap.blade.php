@extends('layouts.portal-dashboard')

@section('title', $pageTitle)

@section('content')
    <div class="portal-dashboard-shell" data-dashboard data-dashboard-portal="admin">
        @include('dashboard.partials.sidebar', ['menuSections' => $menuSections, 'interactiveSidebar' => false])

        <main class="portal-dashboard-main portal-directory-main">
            <div class="portal-directory-header">
                <div>
                    <span class="portal-hero__badge">{{ $activeAcademicYear }} | {{ ucfirst($activeSemester) }}</span>
                    <h1>Rekap Kehadiran</h1>
                    <p>Pantau ringkasan kehadiran kelas dan wali kelas tanpa memenuhi halaman beranda.</p>
                </div>
            </div>

            @include('dashboard.partials.admin-attendance-recap')
        </main>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const tabs = Array.from(document.querySelectorAll('[data-admin-panel-tab]'));
            const panels = Array.from(document.querySelectorAll('[data-admin-panel]'));

            tabs.forEach((tab) => {
                tab.addEventListener('click', () => {
                    const target = tab.dataset.adminPanelTab;

                    tabs.forEach((item) => item.classList.toggle('is-active', item === tab));
                    panels.forEach((panel) => panel.classList.toggle('d-none', panel.dataset.adminPanel !== target));
                });
            });
        });
    </script>
@endpush
