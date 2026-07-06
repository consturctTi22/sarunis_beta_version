@extends('layouts.portal-dashboard')

@section('title', $pageTitle)

@php
    if (!isset($menuSections) || empty($menuSections)) {
        if ($activePortal === 'admin') {
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
                        ['label' => 'Jadwal Pelajaran', 'icon' => 'schedule', 'href' => url('/admin/schedule/generate'), 'active' => false],
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
                        ['label' => 'Pengumuman', 'icon' => 'announcement', 'href' => url('/admin/pengumuman'), 'active' => false],
                    ],
                ],
            ];
        } elseif ($activePortal === 'wakasek-kesiswaan') {
            $menuSections = [
                ['title' => 'Menu Utama', 'items' => [
                    ['label' => 'Dashboard', 'href' => url('/wakasek-kesiswaan/dashboard'), 'icon' => 'home', 'active' => false],
                    ['label' => 'Data Pelanggaran', 'href' => url('/wakasek-kesiswaan/pelanggaran'), 'icon' => 'note', 'active' => true]
                ]]
            ];
        } elseif ($activePortal === 'guru-piket') {
            $menuSections = [
                ['title' => 'Menu Utama', 'items' => [
                    ['label' => 'Dashboard', 'href' => url('/guru-piket/dashboard'), 'icon' => 'home', 'active' => false],
                    ['label' => 'Data Pelanggaran', 'href' => url('/guru-piket/pelanggaran'), 'icon' => 'note', 'active' => true]
                ]]
            ];
        }
    }
@endphp

@section('content')
    <div class="portal-dashboard-shell portal-directory-shell" data-violation-directory>
        @include('dashboard.partials.sidebar', ['menuSections' => $menuSections])

        <main class="portal-dashboard-main portal-directory-main">
            <div class="portal-directory-stack">
                <section class="portal-panel portal-directory-banner">
                    <div class="portal-directory-banner__bar"></div>
                    <div class="portal-directory-banner__copy">
                        <h1>{{ $directoryTitle }}</h1>
                        <p>{{ $directorySubtitle }}</p>
                    </div>
                </section>

                <section class="portal-directory-toolbar">
                    <label class="portal-directory-search" for="violation-directory-search">
                        <span class="portal-directory-search__icon">
                            @include('dashboard.partials.icon', ['name' => 'search'])
                        </span>
                        <input id="violation-directory-search" type="search" placeholder="Cari siswa atau pelanggaran..." data-directory-search>
                    </label>

                    <div class="portal-directory-toolbar__actions">
                        <button class="portal-round-action" type="button" aria-label="Catat Pelanggaran" data-bs-toggle="modal" data-bs-target="#violation-directory-modal" onclick="resetForm()">
                            @include('dashboard.partials.icon', ['name' => 'plus'])
                        </button>
                    </div>
                </section>

                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <section class="portal-directory-section">
                    <div class="table-responsive">
                        <table class="table portal-table portal-directory-table mb-0">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Nama Siswa</th>
                                    <th>Kelas</th>
                                    <th>Jenis Pelanggaran</th>
                                    <th>Poin</th>
                                    <th>Pelapor</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($violations as $violation)
                                    <tr data-search-text="{{ mb_strtolower($violation->student->name . ' ' . $violation->violation_type . ' ' . ($violation->student->schoolClass->name ?? '')) }}">
                                        <td>{{ $violation->violation_date->format('d-m-Y') }}</td>
                                        <td>{{ $violation->student->name }}</td>
                                        <td>{{ $violation->student->schoolClass->name ?? '-' }}</td>
                                        <td>{{ $violation->violation_type }}</td>
                                        <td>{{ $violation->points }}</td>
                                        <td>{{ $violation->reporter->name ?? '-' }}</td>
                                        <td>
                                            <div class="portal-directory-actions">
                                                <button class="portal-directory-action is-edit" type="button" aria-label="Ubah data" onclick='editViolation(@json($violation))' data-bs-toggle="modal" data-bs-target="#violation-directory-modal">
                                                    @include('dashboard.partials.icon', ['name' => 'edit'])
                                                </button>
                                                <form action="{{ url($activePortal === 'admin' ? '/admin/pelanggaran/'.$violation->id : '/'.$activePortal.'/pelanggaran/'.$violation->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus data pelanggaran ini?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="portal-directory-action is-delete" type="submit" aria-label="Hapus data">
                                                        @include('dashboard.partials.icon', ['name' => 'trash'])
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4">Belum ada data pelanggaran.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        {{ $violations->links() }}
                    </div>
                </section>
            </div>
        </main>
    </div>

    <!-- Modal Form -->
    <div class="modal fade" id="violation-directory-modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content portal-directory-modal">
                <form id="violation-form" method="POST" action="{{ url($activePortal === 'admin' ? '/admin/pelanggaran' : '/'.$activePortal.'/pelanggaran') }}">
                    @csrf
                    <input type="hidden" name="_method" id="form-method" value="POST">
                    
                    <div class="modal-header border-0 pb-0">
                        <div>
                            <h2 class="modal-title fs-4 fw-bold" id="form-title">Catat Pelanggaran</h2>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>

                    <div class="modal-body pt-3">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Siswa</label>
                            <select class="form-select" name="student_id" id="student_id" required>
                                <option value="">Pilih Siswa...</option>
                                @foreach($classes as $schoolClass)
                                    <optgroup label="Kelas {{ $schoolClass->name }}">
                                        @foreach($schoolClass->students as $student)
                                            <option value="{{ $student->id }}">{{ $student->name }}</option>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Tanggal Kejadian</label>
                            <input class="form-control" type="date" name="violation_date" id="violation_date" value="{{ date('Y-m-d') }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Jenis Pelanggaran</label>
                            <select class="form-select" name="violation_type" id="violation_type" required>
                                <option value="">Pilih...</option>
                                <option value="Keterlambatan">Keterlambatan</option>
                                <option value="Ketertiban Berpakaian">Ketertiban Berpakaian</option>
                                <option value="Sikap / Perilaku">Sikap / Perilaku</option>
                                <option value="Lainnya">Lainnya</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Deskripsi</label>
                            <textarea class="form-control" name="description" id="description" rows="3" required></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Poin Pelanggaran</label>
                            <input class="form-control" type="number" name="points" id="points" value="0" min="0" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Tindakan / Sanksi</label>
                            <textarea class="form-control" name="action_taken" id="action_taken" rows="2"></textarea>
                        </div>
                    </div>

                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.querySelector('[data-directory-search]');
            const rows = document.querySelectorAll('tbody tr[data-search-text]');

            if (searchInput) {
                searchInput.addEventListener('input', function (e) {
                    const query = e.target.value.toLowerCase();
                    rows.forEach(row => {
                        const text = row.getAttribute('data-search-text') || '';
                        row.style.display = text.includes(query) ? '' : 'none';
                    });
                });
            }
        });

        function resetForm() {
            const form = document.getElementById('violation-form');
            form.reset();
            document.getElementById('form-method').value = 'POST';
            document.getElementById('form-title').innerText = 'Catat Pelanggaran';
            
            // Set action to store
            const activePortal = '{{ $activePortal }}';
            const prefix = activePortal === 'admin' ? '/admin' : '/' + activePortal;
            form.action = '{{ url("") }}' + prefix + '/pelanggaran';
            document.getElementById('violation_date').value = new Date().toISOString().split('T')[0];
        }

        function editViolation(violation) {
            const form = document.getElementById('violation-form');
            document.getElementById('form-method').value = 'PUT';
            document.getElementById('form-title').innerText = 'Ubah Data Pelanggaran';
            
            const activePortal = '{{ $activePortal }}';
            const prefix = activePortal === 'admin' ? '/admin' : '/' + activePortal;
            form.action = '{{ url("") }}' + prefix + '/pelanggaran/' + violation.id;

            document.getElementById('student_id').value = violation.student_id;
            document.getElementById('violation_date').value = violation.violation_date.split('T')[0];
            
            let typeOptions = Array.from(document.getElementById('violation_type').options).map(opt => opt.value);
            if(typeOptions.includes(violation.violation_type)) {
                document.getElementById('violation_type').value = violation.violation_type;
            } else {
                document.getElementById('violation_type').value = 'Lainnya';
            }

            document.getElementById('description').value = violation.description;
            document.getElementById('points').value = violation.points;
            document.getElementById('action_taken').value = violation.action_taken || '';
        }
    </script>
@endpush
