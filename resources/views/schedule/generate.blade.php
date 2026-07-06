@extends('layouts.portal-dashboard')

@section('title', 'Atur Jadwal Mata Pelajaran')

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
    <div class="portal-dashboard-shell portal-directory-shell" data-manual-schedule-page>
        @include('dashboard.partials.sidebar', ['menuSections' => $menuSections])

        <main class="portal-dashboard-main portal-directory-main">
            <div class="portal-directory-stack">
                <section class="portal-panel portal-directory-banner">
                    <div class="portal-directory-banner__bar"></div>
                    <span class="visually-hidden">Ploting Jadwal Pelajaran Parameter Generator</span>
                    <div class="portal-directory-banner__copy">
                        <h1>Atur Jadwal Mata Pelajaran</h1>
                        <p>Buat jadwal pelajaran secara manual per kelas, guru, mata pelajaran, hari, dan jam ke.</p>
                    </div>
                    <div class="portal-directory-banner__count">Manual</div>
                </section>

                <div class="row g-4">
                    <div class="col-lg-5">
                        <section class="portal-panel p-4">
                            <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
                                <div>
                                    <h2 class="fs-5 fw-bold mb-1">Form Pembuatan Jadwal</h2>
                                    <p class="text-secondary mb-0">Validasi bentrok kelas dan guru berjalan saat data disimpan.</p>
                                </div>
                            </div>

                            <div class="alert d-none" data-schedule-feedback></div>

                            <form id="manual-schedule-form">
                                @csrf
                                <input type="hidden" name="start_time" data-start-time>
                                <input type="hidden" name="end_time" data-end-time>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold" for="academic-year">Tahun Akademik</label>
                                    <select class="form-select" id="academic-year" name="academic_year" required>
                                        @foreach ($academicYears as $value => $label)
                                            <option value="{{ $value }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold" for="school-class-id">Kelas</label>
                                    <select class="form-select" id="school-class-id" name="school_class_id" required>
                                        <option value="">Pilih kelas</option>
                                        @foreach ($classes as $class)
                                            <option value="{{ $class->id }}" data-room="Ruang {{ $class->name }}">{{ $class->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold" for="teacher-id">Guru Pengampu</label>
                                    <select class="form-select" id="teacher-id" name="teacher_id" required disabled>
                                        <option value="">Pilih guru</option>
                                        @foreach ($teachers as $teacher)
                                            <option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold" for="subject-id">Mata Pelajaran</label>
                                    <select class="form-select" id="subject-id" name="subject_id" required disabled>
                                        <option value="">Pilih mata pelajaran</option>
                                        @foreach ($subjects as $subject)
                                            <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold" for="day-of-week">Hari</label>
                                        <select class="form-select" id="day-of-week" name="day_of_week" required>
                                            <option value="">Pilih hari</option>
                                            @foreach ($dayOptions as $day)
                                                <option value="{{ $day['value'] }}">{{ $day['label'] }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold" for="lesson-period">Jam Pelajaran</label>
                                        <select class="form-select" id="lesson-period" required>
                                            <option value="">Pilih jam ke-</option>
                                            @foreach ($lessonPeriods as $slot)
                                                <option value="{{ $slot['period'] }}" data-start="{{ $slot['start_time'] }}" data-end="{{ $slot['end_time'] }}">
                                                    {{ $slot['label'] }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="mt-3">
                                    <label class="form-label fw-semibold" for="room">Ruangan</label>
                                    <input class="form-control" id="room" name="room" maxlength="50" placeholder="Contoh: Ruang X IPA 1">
                                </div>

                                <button class="btn btn-primary w-100 mt-4" type="submit" data-submit-schedule>
                                    Simpan Jadwal
                                </button>
                            </form>
                        </section>
                    </div>

                    <div class="col-lg-7">
                        <section class="portal-panel p-4 h-100">
                            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3">
                                <div>
                                    <h2 class="fs-5 fw-bold mb-1">Daftar Jadwal Kelas</h2>
                                    <p class="text-secondary mb-0" data-schedule-summary>Pilih kelas untuk menampilkan jadwal.</p>
                                </div>
                                <a class="btn btn-sm btn-outline-primary d-none" href="#" data-open-class-schedule>
                                    Lihat Halaman Kelas
                                </a>
                            </div>

                            <div class="table-responsive">
                                <table class="table align-middle">
                                    <thead>
                                        <tr>
                                            <th>Hari</th>
                                            <th>Jam</th>
                                            <th>Mapel</th>
                                            <th>Guru</th>
                                            <th>Ruangan</th>
                                        </tr>
                                    </thead>
                                    <tbody data-class-schedule-list>
                                        <tr>
                                            <td colspan="5" class="text-center text-secondary py-5">Belum ada kelas yang dipilih.</td>
                                        </tr>
                                    </tbody>
                                </table>
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
            const form = document.getElementById('manual-schedule-form');
            const classSelect = document.getElementById('school-class-id');
            const teacherSelect = document.getElementById('teacher-id');
            const subjectSelect = document.getElementById('subject-id');
            const yearSelect = document.getElementById('academic-year');
            const periodSelect = document.getElementById('lesson-period');
            const roomInput = document.getElementById('room');
            const startInput = form.querySelector('[data-start-time]');
            const endInput = form.querySelector('[data-end-time]');
            const feedback = document.querySelector('[data-schedule-feedback]');
            const submitButton = document.querySelector('[data-submit-schedule]');
            const scheduleList = document.querySelector('[data-class-schedule-list]');
            const summary = document.querySelector('[data-schedule-summary]');
            const openClassSchedule = document.querySelector('[data-open-class-schedule]');
            const token = '{{ csrf_token() }}';
            const allSubjects = @json($subjects->map(fn($subject) => ['id' => $subject->id, 'name' => $subject->name])->values());
            const teacherSubjects = @json($teachers->mapWithKeys(fn($teacher) => [$teacher->id => $teacher->subjects->map(fn($subject) => ['id' => $subject->id, 'name' => $subject->name])->values()]));

            const escapeHtml = function (value) {
                return String(value ?? '')
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;')
                    .replace(/'/g, '&#039;');
            };

            const showFeedback = function (type, messages) {
                const list = Array.isArray(messages) ? messages : [messages];
                feedback.className = 'alert alert-' + type;
                feedback.innerHTML = list.map((message) => '<div>' + escapeHtml(message) + '</div>').join('');
                feedback.classList.remove('d-none');
            };

            const clearFeedback = function () {
                feedback.classList.add('d-none');
                feedback.textContent = '';
            };

            const fillSubjects = function () {
                const teacherId = teacherSelect.value;
                const subjects = teacherId && teacherSubjects[teacherId] && teacherSubjects[teacherId].length > 0
                    ? teacherSubjects[teacherId]
                    : allSubjects;

                subjectSelect.innerHTML = '<option value="">Pilih mata pelajaran</option>';
                subjects.forEach(function (subject) {
                    subjectSelect.insertAdjacentHTML(
                        'beforeend',
                        '<option value="' + escapeHtml(subject.id) + '">' + escapeHtml(subject.name) + '</option>'
                    );
                });
                subjectSelect.disabled = !teacherId;
            };

            const setSelectedPeriod = function () {
                const option = periodSelect.selectedOptions[0];
                startInput.value = option?.dataset.start || '';
                endInput.value = option?.dataset.end || '';
            };

            const updateClassLink = function () {
                if (!classSelect.value) {
                    openClassSchedule.classList.add('d-none');
                    openClassSchedule.href = '#';
                    return;
                }

                openClassSchedule.href = '/admin/schedule/class/' + classSelect.value + '/' + yearSelect.value;
                openClassSchedule.classList.remove('d-none');
            };

            const renderScheduleRows = function (schedule) {
                const rows = [];

                Object.keys(schedule || {}).forEach(function (day) {
                    (schedule[day] || []).forEach(function (session) {
                        rows.push(
                            '<tr>' +
                            '<td class="fw-semibold">' + escapeHtml(day) + '</td>' +
                            '<td>' + escapeHtml(session.time) + '</td>' +
                            '<td>' + escapeHtml(session.subject) + '</td>' +
                            '<td>' + escapeHtml(session.teacher) + '</td>' +
                            '<td>' + escapeHtml(session.room || '-') + '</td>' +
                            '</tr>'
                        );
                    });
                });

                scheduleList.innerHTML = rows.length > 0
                    ? rows.join('')
                    : '<tr><td colspan="5" class="text-center text-secondary py-5">Belum ada jadwal untuk kelas ini.</td></tr>';
                summary.textContent = rows.length + ' jadwal tampil untuk kelas terpilih.';
            };

            const loadClassSchedule = async function () {
                updateClassLink();

                if (!classSelect.value) {
                    teacherSelect.disabled = true;
                    subjectSelect.disabled = true;
                    scheduleList.innerHTML = '<tr><td colspan="5" class="text-center text-secondary py-5">Belum ada kelas yang dipilih.</td></tr>';
                    summary.textContent = 'Pilih kelas untuk menampilkan jadwal.';
                    return;
                }

                teacherSelect.disabled = false;
                scheduleList.innerHTML = '<tr><td colspan="5" class="text-center text-secondary py-5">Memuat jadwal...</td></tr>';

                try {
                    const response = await fetch('/admin/schedule/class/' + classSelect.value + '/' + yearSelect.value, {
                        headers: { 'Accept': 'application/json' }
                    });
                    const payload = await response.json();

                    if (!response.ok || !payload.success) {
                        throw new Error(payload.message || 'Gagal memuat jadwal.');
                    }

                    renderScheduleRows(payload.data.schedule || {});
                } catch (error) {
                    scheduleList.innerHTML = '<tr><td colspan="5" class="text-center text-danger py-5">Gagal memuat jadwal kelas.</td></tr>';
                    summary.textContent = 'Jadwal belum dapat ditampilkan.';
                }
            };

            classSelect.addEventListener('change', function () {
                clearFeedback();
                const option = classSelect.selectedOptions[0];
                roomInput.value = option?.dataset.room || '';
                teacherSelect.value = '';
                subjectSelect.value = '';
                fillSubjects();
                loadClassSchedule();
            });

            yearSelect.addEventListener('change', loadClassSchedule);
            teacherSelect.addEventListener('change', fillSubjects);
            periodSelect.addEventListener('change', setSelectedPeriod);

            form.addEventListener('submit', async function (event) {
                event.preventDefault();
                clearFeedback();
                setSelectedPeriod();

                if (!startInput.value || !endInput.value) {
                    showFeedback('danger', 'Pilih jam pelajaran terlebih dahulu.');
                    return;
                }

                submitButton.disabled = true;

                try {
                    const response = await fetch('/admin/jadwal-ajar', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': token,
                            'Accept': 'application/json'
                        },
                        body: new FormData(form)
                    });
                    const payload = await response.json().catch(() => ({}));

                    if (!response.ok) {
                        const messages = payload.errors ? Object.values(payload.errors).flat() : [payload.message || 'Jadwal gagal disimpan.'];
                        showFeedback('danger', messages);
                        return;
                    }

                    showFeedback('success', payload.message || 'Jadwal berhasil disimpan.');
                    form.reset();
                    classSelect.value = payload.data.school_class_id;
                    yearSelect.value = payload.data.academic_year;
                    teacherSelect.disabled = false;
                    subjectSelect.disabled = true;
                    await loadClassSchedule();
                } catch (error) {
                    showFeedback('danger', 'Terjadi kesalahan koneksi.');
                } finally {
                    submitButton.disabled = false;
                }
            });

            setSelectedPeriod();
            fillSubjects();
            updateClassLink();
        });
    </script>
@endpush
