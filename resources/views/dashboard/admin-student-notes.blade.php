@extends('layouts.portal-dashboard')

@section('title', $pageTitle)

@section('content')
    <div class="portal-dashboard-shell portal-directory-shell" data-notes-page data-endpoint="{{ url('/admin/catatan') }}">
        @include('dashboard.partials.sidebar', ['menuSections' => $menuSections])

        <main class="portal-dashboard-main portal-directory-main">
            <div class="portal-directory-stack">
                <section class="portal-panel portal-directory-banner">
                    <div class="portal-directory-banner__bar"></div>
                    <div class="portal-directory-banner__copy">
                        <h1>Catatan Siswa</h1>
                        <p>Catatan pembinaan, tindak lanjut, dan riwayat perhatian untuk siswa.</p>
                    </div>
                    <div class="portal-directory-banner__count">{{ $notes->count() }} catatan</div>
                </section>

                <section class="portal-directory-toolbar">
                    <label class="portal-directory-search">
                        <span class="portal-directory-search__icon">@include('dashboard.partials.icon', ['name' => 'search'])</span>
                        <input type="search" placeholder="Pencarian..." data-search>
                    </label>
                    <label class="portal-directory-filter">
                        <select data-status-filter>
                            <option value="">Semua status</option>
                            <option value="open">Terbuka</option>
                            <option value="resolved">Selesai</option>
                        </select>
                    </label>
                    <label class="portal-directory-filter">
                        <select data-category-filter>
                            <option value="">Semua kategori</option>
                            @foreach ($notes->pluck('category')->filter()->unique()->sort()->values() as $category)
                                <option value="{{ $category }}">{{ $category }}</option>
                            @endforeach
                        </select>
                    </label>
                    <button class="portal-round-action" type="button" data-create data-bs-toggle="modal" data-bs-target="#note-modal">@include('dashboard.partials.icon', ['name' => 'plus'])</button>
                </section>

                <div class="alert alert-success d-none" data-feedback></div>

                @include('dashboard.partials.student-notes-table', ['notes' => $notes])
            </div>
        </main>
    </div>

    @include('dashboard.partials.student-notes-modal', ['students' => $students, 'teachers' => $teachers, 'showTeacherField' => true])
@endsection

@include('dashboard.partials.student-notes-script')
