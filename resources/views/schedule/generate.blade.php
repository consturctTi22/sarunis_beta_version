@extends('layouts.portal-dashboard')

@section('title', 'Ploting Jadwal Pelajaran')

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
    <div class="portal-dashboard-shell portal-directory-shell" data-schedule-generate-page>
        @include('dashboard.partials.sidebar', ['menuSections' => $menuSections])

        <main class="portal-dashboard-main portal-directory-main">
            <div class="portal-directory-stack">
                <section class="portal-panel portal-directory-banner">
                    <div class="portal-directory-banner__bar"></div>
                    <div class="portal-directory-banner__copy">
                        <h1>Ploting Jadwal Pelajaran</h1>
                        <p>Generate, periksa, dan optimalkan jadwal mengajar guru secara otomatis tanpa konflik.</p>
                    </div>
                    <div class="portal-directory-banner__count">Auto Plotting</div>
                </section>

                <div class="row g-4">
                    <div class="col-lg-5">
                        <section class="portal-panel p-4">
                            <h2 class="fs-5 fw-bold mb-3">Parameter Generator</h2>
                            <form id="generate-schedule-form">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label fw-semibold" for="academic-year">Tahun Akademik</label>
                                    <select class="form-select" id="academic-year" name="academic_year" required>
                                        @foreach ($academicYears as $value => $label)
                                            <option value="{{ $value }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    <small class="text-secondary d-block mt-1">Pilih tahun ajaran target plotting.</small>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold" for="school-class-id">Kelas Target (Opsional)</label>
                                    <select class="form-select" id="school-class-id" name="school_class_id">
                                        <option value="">Semua Kelas</option>
                                        @foreach ($classes as $class)
                                            <option value="{{ $class->id }}">{{ $class->name }}</option>
                                        @endforeach
                                    </select>
                                    <small class="text-secondary d-block mt-1">Kosongkan untuk generate seluruh kelas sekaligus.</small>
                                </div>

                                <div class="form-check form-switch mb-4">
                                    <input class="form-check-input" type="checkbox" id="clear-existing" name="clear_existing" value="1" checked>
                                    <label class="form-check-label fw-semibold" for="clear-existing">Bersihkan Jadwal Lama</label>
                                    <small class="text-secondary d-block mt-1">Hapus plotting jadwal mengajar yang sudah ada untuk parameter terpilih sebelum generate.</small>
                                </div>

                                <div class="d-flex gap-2">
                                    <button class="btn btn-outline-primary w-50" type="button" id="btn-validate">
                                        Validasi Data
                                    </button>
                                    <button class="btn btn-primary w-50" type="submit" id="btn-submit">
                                        Mulai Plotting
                                    </button>
                                </div>
                            </form>
                        </section>

                        <section class="portal-panel p-4 mt-4">
                            <h2 class="fs-5 fw-bold mb-3">Audit & Optimasi Jadwal</h2>
                            <p class="text-secondary mb-3">Setelah melakukan plotting otomatis, Anda dapat menganalisis kelemahan jadwal, konflik, dan beban kerja guru.</p>
                            <div class="d-grid gap-2">
                                <a href="#" id="link-analyze" class="btn btn-light text-start d-flex justify-content-between align-items-center">
                                    <span>🔍 Analisis & Deteksi Konflik</span>
                                    <span>&rarr;</span>
                                </a>
                                <div class="dropdown">
                                    <button class="btn btn-light text-start d-flex justify-content-between align-items-center w-100 dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                        <span>📅 Lihat Jadwal Kelas</span>
                                    </button>
                                    <ul class="dropdown-menu w-100" style="max-height: 200px; overflow-y: auto;">
                                        @foreach ($classes as $class)
                                            <li><a class="dropdown-item link-class-schedule" href="#" data-class-id="{{ $class->id }}">{{ $class->name }}</a></li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </section>
                    </div>

                    <div class="col-lg-7">
                        <section class="portal-panel p-4 h-100" style="min-height: 400px;">
                            <h2 class="fs-5 fw-bold mb-3">Hasil Plotting & Validasi</h2>
                            
                            <div id="status-placeholder" class="text-center py-5 text-secondary">
                                <p class="mb-0">Silakan pilih aksi: **Validasi Data** atau **Mulai Plotting** untuk melihat analisis.</p>
                            </div>

                            <div id="loading-spinner" class="text-center py-5 d-none">
                                <div class="spinner-border text-primary" role="status"></div>
                                <p class="mt-3 text-secondary">Memproses data jadwal... Mohon tunggu.</p>
                            </div>

                            <div id="results-display" class="d-none">
                                <div class="alert d-none" id="result-alert"></div>

                                <div class="row g-3 mb-4">
                                    <div class="col-6 col-sm-3">
                                        <div class="bg-light p-3 rounded text-center">
                                            <small class="text-secondary d-block text-uppercase fw-bold fs-7">Total Kelas</small>
                                            <strong class="fs-4" id="stat-classes">0</strong>
                                        </div>
                                    </div>
                                    <div class="col-6 col-sm-3">
                                        <div class="bg-light p-3 rounded text-center">
                                            <small class="text-secondary d-block text-uppercase fw-bold fs-7">Sukses Slot</small>
                                            <strong class="fs-4 text-success" id="stat-success">0</strong>
                                        </div>
                                    </div>
                                    <div class="col-6 col-sm-3">
                                        <div class="bg-light p-3 rounded text-center">
                                            <small class="text-secondary d-block text-uppercase fw-bold fs-7">Gagal Slot</small>
                                            <strong class="fs-4 text-danger" id="stat-failed">0</strong>
                                        </div>
                                    </div>
                                    <div class="col-6 col-sm-3">
                                        <div class="bg-light p-3 rounded text-center">
                                            <small class="text-secondary d-block text-uppercase fw-bold fs-7">Konflik</small>
                                            <strong class="fs-4 text-warning" id="stat-conflicts">0</strong>
                                        </div>
                                    </div>
                                </div>

                                <h3 class="fs-6 fw-bold mb-2">Detail Hasil per Kelas</h3>
                                <div class="accordion" id="accordion-details" style="max-height: 350px; overflow-y: auto;">
                                    <!-- Dynamic accordion items -->
                                </div>
                            </div>

                            <div id="validation-display" class="d-none">
                                <div class="alert alert-success d-none" id="val-success-alert">Semua data valid dan siap diproses.</div>
                                <div class="mb-4">
                                    <h3 class="fs-6 fw-bold text-danger mb-2">Error Validasi</h3>
                                    <div class="list-group" id="val-errors-list">
                                        <div class="list-group-item text-secondary">Tidak ada error terdeteksi.</div>
                                    </div>
                                </div>
                                <div>
                                    <h3 class="fs-6 fw-bold text-warning mb-2">Peringatan / Warnings</h3>
                                    <div class="list-group" id="val-warnings-list">
                                        <div class="list-group-item text-secondary">Tidak ada peringatan.</div>
                                    </div>
                                </div>
                            </div>
                        </section>
                    </div>
                </div>
            </div>
        </main>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('generate-schedule-form');
            const btnValidate = document.getElementById('btn-validate');
            const btnSubmit = document.getElementById('btn-submit');
            const statusPlaceholder = document.getElementById('status-placeholder');
            const loadingSpinner = document.getElementById('loading-spinner');
            const resultsDisplay = document.getElementById('results-display');
            const validationDisplay = document.getElementById('validation-display');
            const linkAnalyze = document.getElementById('link-analyze');
            
            // Set initial audit links based on initial academic year value
            const updateAuditLinks = function () {
                const year = document.getElementById('academic-year').value;
                linkAnalyze.href = '/admin/schedule/analyze/' + year;
                
                document.querySelectorAll('.link-class-schedule').forEach(function (link) {
                    const classId = link.dataset.classId;
                    link.href = '/admin/schedule/class/' + classId + '/' + year;
                });
            };
            
            document.getElementById('academic-year').addEventListener('change', updateAuditLinks);
            updateAuditLinks();

            const showLoading = function () {
                statusPlaceholder.classList.add('d-none');
                resultsDisplay.classList.add('d-none');
                validationDisplay.classList.add('d-none');
                loadingSpinner.classList.remove('d-none');
                btnValidate.disabled = true;
                btnSubmit.disabled = true;
            };

            const hideLoading = function () {
                loadingSpinner.classList.add('d-none');
                btnValidate.disabled = false;
                btnSubmit.disabled = false;
            };

            btnValidate.addEventListener('click', async function () {
                showLoading();
                const year = document.getElementById('academic-year').value;
                
                try {
                    const response = await fetch('/admin/schedule/generate', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            academic_year: year,
                            validate_only: 1,
                            clear_existing: 0
                        })
                    });

                    const payload = await response.json();
                    hideLoading();
                    validationDisplay.classList.remove('d-none');

                    const errorsList = document.getElementById('val-errors-list');
                    const warningsList = document.getElementById('val-warnings-list');
                    const successAlert = document.getElementById('val-success-alert');

                    errorsList.innerHTML = '';
                    warningsList.innerHTML = '';

                    if (payload.success) {
                        successAlert.classList.remove('d-none');
                    } else {
                        successAlert.classList.add('d-none');
                    }

                    if (payload.errors && payload.errors.length > 0) {
                        payload.errors.forEach(function (err) {
                            errorsList.insertAdjacentHTML('beforeend', '<div class="list-group-item list-group-item-danger">' + err + '</div>');
                        });
                    } else {
                        errorsList.innerHTML = '<div class="list-group-item text-secondary">Tidak ada error terdeteksi.</div>';
                    }

                    if (payload.warnings && payload.warnings.length > 0) {
                        payload.warnings.forEach(function (warn) {
                            warningsList.insertAdjacentHTML('beforeend', '<div class="list-group-item list-group-item-warning">' + warn + '</div>');
                        });
                    } else {
                        warningsList.innerHTML = '<div class="list-group-item text-secondary">Tidak ada peringatan.</div>';
                    }

                } catch (error) {
                    hideLoading();
                    statusPlaceholder.classList.remove('d-none');
                    alert('Gagal menjalankan validasi.');
                }
            });

            form.addEventListener('submit', async function (event) {
                event.preventDefault();
                showLoading();

                const year = document.getElementById('academic-year').value;
                const classId = document.getElementById('school-class-id').value;
                const clearExisting = document.getElementById('clear-existing').checked ? 1 : 0;

                try {
                    const response = await fetch('/admin/schedule/generate', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            academic_year: year,
                            school_class_id: classId || null,
                            clear_existing: clearExisting,
                            validate_only: 0
                        })
                    });

                    const payload = await response.json();
                    hideLoading();

                    if (!response.ok) {
                        alert(payload.message || 'Gagal melakukan plotting otomatis.');
                        statusPlaceholder.classList.remove('d-none');
                        return;
                    }

                    resultsDisplay.classList.remove('d-none');
                    const alertEl = document.getElementById('result-alert');
                    alertEl.textContent = payload.message;
                    alertEl.className = 'alert alert-success';
                    alertEl.classList.remove('d-none');

                    document.getElementById('stat-classes').textContent = payload.data.total_classes || 0;
                    document.getElementById('stat-success').textContent = payload.data.successful_slots || 0;
                    document.getElementById('stat-failed').textContent = payload.data.failed_slots || 0;
                    document.getElementById('stat-conflicts').textContent = payload.data.conflicts_detected || 0;

                    const accordion = document.getElementById('accordion-details');
                    accordion.innerHTML = '';

                    if (payload.data.details && payload.data.details.length > 0) {
                        payload.data.details.forEach(function (detail, idx) {
                            const collapseId = 'collapse-' + idx;
                            const headerId = 'heading-' + idx;
                            
                            let subjectsList = '';
                            if (detail.scheduled_subjects && detail.scheduled_subjects.length > 0) {
                                detail.scheduled_subjects.forEach(function (sub) {
                                    const badgeClass = sub.status === 'success' ? 'bg-success' : 'bg-danger';
                                    const teacherName = sub.teacher ? (' - ' + sub.teacher) : '';
                                    const timeLabel = sub.time ? (' (' + sub.day + ', ' + sub.time + ')') : '';
                                    subjectsList += '<li class="list-group-item d-flex justify-content-between align-items-center">' +
                                        '<span>' + sub.subject + teacherName + timeLabel + '</span>' +
                                        '<span class="badge ' + badgeClass + '">' + sub.status + '</span>' +
                                        '</li>';
                                });
                            } else {
                                subjectsList = '<li class="list-group-item text-secondary">Tidak ada mata pelajaran yang dijadwalkan.</li>';
                            }

                            const html = '<div class="accordion-item">' +
                                '<h2 class="accordion-header" id="' + headerId + '">' +
                                '<button class="accordion-button collapsed fw-bold py-2 fs-7" type="button" data-bs-toggle="collapse" data-bs-target="#' + collapseId + '">' +
                                detail.class_name + ' (Sukses: ' + detail.successful_slots + ', Gagal: ' + detail.failed_slots + ')' +
                                '</button>' +
                                '</h2>' +
                                '<div id="' + collapseId + '" class="accordion-collapse collapse" data-bs-parent="#accordion-details">' +
                                '<div class="accordion-body p-0">' +
                                '<ul class="list-group list-group-flush fs-7">' +
                                subjectsList +
                                '</ul>' +
                                '</div>' +
                                '</div>' +
                                '</div>';
                            
                            accordion.insertAdjacentHTML('beforeend', html);
                        });
                    }

                } catch (error) {
                    hideLoading();
                    statusPlaceholder.classList.remove('d-none');
                    alert('Terjadi kesalahan koneksi.');
                }
            });
        });
    </script>
@endpush
