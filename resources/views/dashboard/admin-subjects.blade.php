@extends('layouts.portal-dashboard')

@section('title', $pageTitle)

@section('content')
    <div class="portal-dashboard-shell portal-directory-shell" data-subject-directory>
        @include('dashboard.partials.sidebar', ['menuSections' => $menuSections])

        <main class="portal-dashboard-main portal-directory-main">
            <div class="portal-directory-stack">
                <section class="portal-panel portal-directory-banner">
                    <div class="portal-directory-banner__bar"></div>
                    <div class="portal-directory-banner__copy">
                        <h1>{{ $directoryTitle }}</h1>
                        <p>{{ $directorySubtitle }}</p>
                    </div>
                    <div class="portal-directory-banner__count">{{ $totalSubjects }} mapel</div>
                </section>

                <section class="portal-directory-toolbar">
                    <label class="portal-directory-search" for="subject-directory-search">
                        <span class="portal-directory-search__icon">
                            @include('dashboard.partials.icon', ['name' => 'search'])
                        </span>
                        <input id="subject-directory-search" type="search" placeholder="Pencarian..." data-directory-search>
                    </label>

                    <label class="portal-directory-filter" for="subject-directory-filter">
                        <span class="portal-directory-filter__icon">
                            @include('dashboard.partials.icon', ['name' => 'filter'])
                        </span>
                        <select id="subject-directory-filter" data-directory-filter>
                            <option value="">Semua status</option>
                            @foreach ($usageOptions as $usageOption)
                                <option value="{{ $usageOption['value'] }}">{{ $usageOption['label'] }}</option>
                            @endforeach
                        </select>
                        <span class="portal-directory-filter__arrow">
                            @include('dashboard.partials.icon', ['name' => 'chevron-down'])
                        </span>
                    </label>

                    <div class="portal-directory-toolbar__actions">
                        <button class="portal-round-action portal-round-action--outline" type="button" aria-label="Unduh semua data mapel" data-directory-export-all>
                            @include('dashboard.partials.icon', ['name' => 'download'])
                        </button>
                        <button class="portal-round-action" type="button" aria-label="Tambah data mapel" data-subject-create data-bs-toggle="modal" data-bs-target="#subject-directory-modal">
                            @include('dashboard.partials.icon', ['name' => 'plus'])
                        </button>
                    </div>
                </section>

                <div class="portal-directory-feedback d-none" data-directory-feedback></div>

                @forelse ($directoryGroups as $group)
                    <section class="portal-directory-section" data-directory-section data-usage="{{ $group['usage'] }}" data-section-key="{{ $group['key'] }}">
                        <div class="portal-directory-section__head">
                            <div>
                                <h2>{{ $group['label'] }}</h2>
                                <p>{{ $group['subtitle'] }}</p>
                            </div>

                            <button class="portal-directory-export" type="button" aria-label="Unduh data {{ $group['label'] }}" data-directory-export-section="{{ $group['key'] }}">
                                @include('dashboard.partials.icon', ['name' => 'download'])
                            </button>
                        </div>

                        <div class="table-responsive">
                            <table class="table portal-table portal-directory-table portal-subject-table mb-0">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Kode</th>
                                        <th>Nama Mapel</th>
                                        <th>Jam Pelajaran</th>
                                        <th>Hari</th>
                                        <th>Jam</th>
                                        <th>Kelas (Jadwal)</th>
                                        <th>Deskripsi</th>
                                        <th>Guru Mapel</th>
                                        <th>Kelas Terkait (Kurikulum)</th>
                                        <th>Total Siswa Diajar</th>
                                        <th>Jumlah Jadwal</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($group['subjects'] as $subject)
                                        <tr data-subject-row data-usage="{{ $subject['usage'] }}" data-section-key="{{ $group['key'] }}" data-search-text="{{ $subject['search_text'] }}">
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $subject['code'] }}</td>
                                            <td>
                                                <div class="portal-directory-name">{{ $subject['name'] }}</div>
                                            </td>
                                            <td>{{ $subject['lesson_hours_label'] }}</td>
                                            <td>{{ $subject['day_name'] }}</td>
                                            <td>{{ $subject['time_label'] }}</td>
                                            <td>{{ $subject['class_name'] }}</td>
                                            <td>{{ $subject['description'] }}</td>
                                            <td>{{ $subject['teacher_label'] }}</td>
                                            <td>{{ $subject['class_label'] }}</td>
                                            <td>{{ $subject['students_taught_label'] }}</td>
                                            <td>{{ $subject['schedule_count'] }} jadwal</td>
                                            <td>
                                                <span class="portal-directory-status {{ $subject['usage'] === 'belum-dipakai' ? 'is-neutral' : '' }}">{{ $subject['usage_label'] }}</span>
                                            </td>
                                            <td>
                                                <div class="portal-directory-actions">
                                                    <button class="portal-directory-action is-edit" type="button" aria-label="Ubah data {{ $subject['name'] }}" data-subject-edit="{{ $subject['id'] }}">
                                                        @include('dashboard.partials.icon', ['name' => 'edit'])
                                                    </button>
                                                    <button class="portal-directory-action is-delete" type="button" aria-label="Hapus data {{ $subject['name'] }}" data-subject-delete="{{ $subject['id'] }}" data-subject-name="{{ $subject['name'] }}">
                                                        @include('dashboard.partials.icon', ['name' => 'trash'])
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </section>
                @empty
                    <section class="portal-panel portal-directory-empty">
                        <h2>Belum ada data mata pelajaran</h2>
                        <p>Tambahkan mapel pertama dari tombol tambah di kanan atas.</p>
                    </section>
                @endforelse

                @if (count($directoryGroups) > 0)
                    <section class="portal-panel portal-directory-empty d-none" data-directory-empty>
                        <h2>Data tidak ditemukan</h2>
                        <p>Coba ubah kata kunci pencarian atau filter status yang sedang dipakai.</p>
                    </section>
                @endif
            </div>
        </main>
    </div>

    <div class="modal fade" id="subject-directory-modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content portal-directory-modal">
                <form data-subject-form>
                    <div class="modal-header border-0 pb-0">
                        <div>
                            <h2 class="modal-title fs-4 fw-bold" data-subject-form-title>Tambah Mata Pelajaran</h2>
                            <p class="text-secondary mb-0">Lengkapi kode dan detail mapel sebelum disimpan.</p>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>

                    <div class="modal-body pt-3">
                        <div class="alert alert-danger d-none" data-subject-form-errors></div>
                        <input type="hidden" name="subject_id" data-subject-id>

                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold" for="subject-code">Kode</label>
                                <input class="form-control" id="subject-code" name="code" type="text" required>
                            </div>
                            <div class="col-md-8">
                                <label class="form-label fw-semibold" for="subject-name">Nama Mapel</label>
                                <input class="form-control" id="subject-name" name="name" type="text" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold" for="subject-lesson-hours">Jam Pelajaran</label>
                                <input class="form-control" id="subject-lesson-hours" name="lesson_hours" type="number" min="1" max="20" placeholder="Contoh: 3">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold" for="subject-school-class-id">Kelas (Jadwal Tetap)</label>
                                <select class="form-select" id="subject-school-class-id" name="school_class_id">
                                    <option value="">Pilih kelas</option>
                                    @foreach ($classOptions as $classOption)
                                        <option value="{{ $classOption['id'] }}">{{ $classOption['name'] }}</option>
                                    @endforeach
                                </select>
                                <small class="text-secondary d-block mt-1" style="font-size: 0.75rem;">Tempat jadwal tetap mapel dilaksanakan.</small>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold" for="subject-day-of-week">Hari</label>
                                <select class="form-select" id="subject-day-of-week" name="day_of_week">
                                    <option value="">Pilih hari</option>
                                    <option value="0">Senin</option>
                                    <option value="1">Selasa</option>
                                    <option value="2">Rabu</option>
                                    <option value="3">Kamis</option>
                                    <option value="4">Jumat</option>
                                    <option value="5">Sabtu</option>
                                    <option value="6">Minggu</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold" for="subject-start-time">Jam Mulai</label>
                                <input class="form-control" id="subject-start-time" name="start_time" type="time">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold" for="subject-end-time">Jam Selesai</label>
                                <input class="form-control" id="subject-end-time" name="end_time" type="time">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold" for="subject-description">Deskripsi</label>
                                <input class="form-control" id="subject-description" name="description" type="text">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold" for="subject-teacher-search">Guru Pengampu</label>
                                <div class="portal-directory-picker">
                                    <div class="portal-directory-picker__toolbar">
                                        <input class="form-control" id="subject-teacher-search" type="search" placeholder="Cari guru yang sudah diinput..." data-subject-teacher-search>
                                        <small>Pilih dari data guru yang sudah ada. Guru yang dipilih akan menjadi guru mapel untuk mapel ini.</small>
                                    </div>
                                    <div class="portal-directory-picker__list">
                                        @forelse ($teacherOptions as $teacherOption)
                                            <label class="portal-directory-picker__item" data-subject-teacher-item data-search-text="{{ strtolower($teacherOption['name'].' '.$teacherOption['role']) }}">
                                                <input class="form-check-input" type="checkbox" value="{{ $teacherOption['id'] }}" data-subject-teacher-checkbox>
                                                <span>
                                                    <strong>{{ $teacherOption['name'] }}</strong>
                                                    <small>{{ $teacherOption['role'] }}</small>
                                                </span>
                                            </label>
                                        @empty
                                            <div class="portal-directory-picker__empty">Belum ada data guru yang dapat dipilih.</div>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold" for="subject-class-search">Kelas Terkait (Kurikulum)</label>
                                <div class="portal-directory-picker">
                                    <div class="portal-directory-picker__toolbar">
                                        <input class="form-control" id="subject-class-search" type="search" placeholder="Cari kelas yang sudah diinput..." data-subject-class-search>
                                        <small>Pilih kelas yang mempelajari mata pelajaran ini (relasi kurikulum).</small>
                                    </div>
                                    <div class="portal-directory-picker__list">
                                        @forelse ($classOptions as $classOption)
                                            <label class="portal-directory-picker__item" data-subject-class-item data-search-text="{{ strtolower($classOption['name'].' '.$classOption['level']) }}">
                                                <input class="form-check-input" type="checkbox" value="{{ $classOption['id'] }}" data-subject-class-checkbox>
                                                <span>
                                                    <strong>{{ $classOption['name'] }}</strong>
                                                    <small>Tingkat {{ $classOption['level'] }}</small>
                                                </span>
                                            </label>
                                        @empty
                                            <div class="portal-directory-picker__empty">Belum ada data kelas yang dapat dipilih.</div>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary portal-directory-submit" data-subject-submit>Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const directory = document.querySelector('[data-subject-directory]');

            if (!directory) {
                return;
            }

            const searchInput = directory.querySelector('[data-directory-search]');
            const filterSelect = directory.querySelector('[data-directory-filter]');
            const feedback = directory.querySelector('[data-directory-feedback]');
            const emptyState = directory.querySelector('[data-directory-empty]');
            const sections = Array.from(directory.querySelectorAll('[data-directory-section]'));
            const rows = Array.from(directory.querySelectorAll('[data-subject-row]'));
            const exportAllButton = directory.querySelector('[data-directory-export-all]');
            const sectionExportButtons = Array.from(directory.querySelectorAll('[data-directory-export-section]'));
            const createButtons = Array.from(directory.querySelectorAll('[data-subject-create]'));
            const editButtons = Array.from(directory.querySelectorAll('[data-subject-edit]'));
            const deleteButtons = Array.from(directory.querySelectorAll('[data-subject-delete]'));
            const subjectMap = @json($subjectPayload);
            const modalElement = document.getElementById('subject-directory-modal');
            const modal = modalElement && window.bootstrap ? window.bootstrap.Modal.getOrCreateInstance(modalElement) : null;
            const form = document.querySelector('[data-subject-form]');
            const formTitle = document.querySelector('[data-subject-form-title]');
            const formErrors = document.querySelector('[data-subject-form-errors]');
            const submitButton = document.querySelector('[data-subject-submit]');
            const idInput = document.querySelector('[data-subject-id]');
            const teacherSearchInput = document.querySelector('[data-subject-teacher-search]');
            const teacherPickerItems = Array.from(document.querySelectorAll('[data-subject-teacher-item]'));
            const teacherCheckboxes = Array.from(document.querySelectorAll('[data-subject-teacher-checkbox]'));
            const classSearchInput = document.querySelector('[data-subject-class-search]');
            const classPickerItems = Array.from(document.querySelectorAll('[data-subject-class-item]'));
            const classCheckboxes = Array.from(document.querySelectorAll('[data-subject-class-checkbox]'));
            const csrfToken = '{{ csrf_token() }}';

            const showFeedback = function (message) {
                if (!feedback) {
                    return;
                }

                feedback.textContent = message;
                feedback.classList.remove('d-none');
            };

            const hideFeedback = function () {
                if (!feedback) {
                    return;
                }

                feedback.textContent = '';
                feedback.classList.add('d-none');
            };

            const applyFilters = function () {
                const query = (searchInput?.value || '').trim().toLowerCase();
                const usage = filterSelect?.value || '';
                let visibleRows = 0;

                sections.forEach(function (section) {
                    const sectionRows = Array.from(section.querySelectorAll('[data-subject-row]'));
                    let sectionHasVisibleRow = false;

                    sectionRows.forEach(function (row) {
                        const matchesQuery = query === '' || (row.dataset.searchText || '').includes(query);
                        const matchesUsage = usage === '' || (row.dataset.usage || '') === usage;
                        const isVisible = matchesQuery && matchesUsage;

                        row.classList.toggle('d-none', !isVisible);

                        if (isVisible) {
                            visibleRows += 1;
                            sectionHasVisibleRow = true;
                        }
                    });

                    section.classList.toggle('d-none', !sectionHasVisibleRow);
                });

                if (emptyState) {
                    emptyState.classList.toggle('d-none', visibleRows !== 0);
                }

                if (query === '' && usage === '') {
                    hideFeedback();

                    return;
                }

                showFeedback('Menampilkan ' + visibleRows + ' data mata pelajaran sesuai filter aktif.');
            };

            const csvEscape = function (value) {
                const normalized = String(value ?? '').replace(/\s+/g, ' ').trim();

                return '"' + normalized.replace(/"/g, '""') + '"';
            };

            const exportRows = function (rowCollection, filename) {
                const visibleRows = rowCollection.filter(function (row) {
                    return !row.classList.contains('d-none');
                });

                if (visibleRows.length === 0) {
                    showFeedback('Tidak ada data yang bisa diunduh dari filter saat ini.');

                    return;
                }

                const lines = [[
                    'No',
                    'Kode',
                    'Nama Mapel',
                    'Jam Pelajaran',
                    'Hari',
                    'Jam',
                    'Kelas',
                    'Deskripsi',
                    'Guru Mapel',
                    'Kelas Terkait',
                    'Total Siswa Diajar',
                    'Jumlah Jadwal',
                    'Status',
                ].map(csvEscape).join(',')];

                visibleRows.forEach(function (row) {
                    const cells = Array.from(row.querySelectorAll('td'));

                    lines.push([
                        cells[0]?.innerText || '',
                        cells[1]?.innerText || '',
                        cells[2]?.innerText || '',
                        cells[3]?.innerText || '',
                        cells[4]?.innerText || '',
                        cells[5]?.innerText || '',
                        cells[6]?.innerText || '',
                        cells[7]?.innerText || '',
                        cells[8]?.innerText || '',
                        cells[9]?.innerText || '',
                        cells[10]?.innerText || '',
                        cells[11]?.innerText || '',
                        cells[12]?.innerText || '',
                    ].map(csvEscape).join(','));
                });

                const blob = new Blob([lines.join('\n')], { type: 'text/csv;charset=utf-8;' });
                const url = window.URL.createObjectURL(blob);
                const link = document.createElement('a');

                link.href = url;
                link.download = filename;
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                window.URL.revokeObjectURL(url);
            };

            const resetForm = function () {
                if (!form) {
                    return;
                }

                form.reset();
                idInput.value = '';
                formTitle.textContent = 'Tambah Mata Pelajaran';
                submitButton.textContent = 'Simpan';
                submitButton.disabled = false;
                formErrors.classList.add('d-none');
                formErrors.innerHTML = '';
                if (form.elements.school_class_id) {
                    form.elements.school_class_id.value = '';
                }
                if (form.elements.day_of_week) {
                    form.elements.day_of_week.value = '';
                }
                if (form.elements.start_time) {
                    form.elements.start_time.value = '';
                }
                if (form.elements.end_time) {
                    form.elements.end_time.value = '';
                }
                teacherCheckboxes.forEach(function (checkbox) {
                    checkbox.checked = false;
                });
                teacherSearchInput.value = '';
                teacherPickerItems.forEach(function (item) {
                    item.classList.remove('d-none');
                });
                classCheckboxes.forEach(function (checkbox) {
                    checkbox.checked = false;
                });
                classSearchInput.value = '';
                classPickerItems.forEach(function (item) {
                    item.classList.remove('d-none');
                });
            };

            const fillForm = function (subject) {
                if (!form || !subject) {
                    return;
                }

                form.elements.code.value = subject.code || '';
                form.elements.name.value = subject.name || '';
                form.elements.lesson_hours.value = subject.lesson_hours || '';
                form.elements.description.value = subject.description || '';
                if (form.elements.school_class_id) {
                    form.elements.school_class_id.value = subject.school_class_id || '';
                }
                if (form.elements.day_of_week) {
                    form.elements.day_of_week.value = (subject.day_of_week !== null && subject.day_of_week !== undefined) ? subject.day_of_week : '';
                }
                if (form.elements.start_time) {
                    form.elements.start_time.value = subject.start_time || '';
                }
                if (form.elements.end_time) {
                    form.elements.end_time.value = subject.end_time || '';
                }
                const selectedTeacherIds = new Set((subject.teacher_ids || []).map(function (value) {
                    return String(value);
                }));
                const selectedClassIds = new Set((subject.class_ids || []).map(function (value) {
                    return String(value);
                }));

                teacherCheckboxes.forEach(function (checkbox) {
                    checkbox.checked = selectedTeacherIds.has(checkbox.value);
                });
                classCheckboxes.forEach(function (checkbox) {
                    checkbox.checked = selectedClassIds.has(checkbox.value);
                });
            };

            const renderErrors = function (payload) {
                if (!formErrors) {
                    return;
                }

                const messages = payload?.errors
                    ? Object.values(payload.errors).flat()
                    : [payload?.message || 'Terjadi kesalahan saat menyimpan data mata pelajaran.'];

                formErrors.innerHTML = messages.map(function (message) {
                    return '<div>' + message + '</div>';
                }).join('');
                formErrors.classList.remove('d-none');
            };

            const selectedTeacherIds = function () {
                return teacherCheckboxes
                    .filter(function (checkbox) { return checkbox.checked; })
                    .map(function (checkbox) { return checkbox.value; });
            };

            const applyTeacherPickerSearch = function () {
                const query = (teacherSearchInput?.value || '').trim().toLowerCase();

                teacherPickerItems.forEach(function (item) {
                    const matches = query === '' || (item.dataset.searchText || '').includes(query);
                    item.classList.toggle('d-none', !matches);
                });
            };

            const selectedClassIds = function () {
                return classCheckboxes
                    .filter(function (checkbox) { return checkbox.checked; })
                    .map(function (checkbox) { return checkbox.value; });
            };

            const applyClassPickerSearch = function () {
                const query = (classSearchInput?.value || '').trim().toLowerCase();

                classPickerItems.forEach(function (item) {
                    const matches = query === '' || (item.dataset.searchText || '').includes(query);
                    item.classList.toggle('d-none', !matches);
                });
            };

            if (searchInput) {
                searchInput.addEventListener('input', applyFilters);
            }

            if (filterSelect) {
                filterSelect.addEventListener('change', applyFilters);
            }

            if (teacherSearchInput) {
                teacherSearchInput.addEventListener('input', applyTeacherPickerSearch);
            }

            if (classSearchInput) {
                classSearchInput.addEventListener('input', applyClassPickerSearch);
            }

            exportAllButton?.addEventListener('click', function () {
                window.location.href = '/admin/export/mapel/xls';
            });

            sectionExportButtons.forEach(function (button) {
                button.addEventListener('click', function () {
                    const key = button.dataset.directoryExportSection;
                    const section = directory.querySelector('[data-section-key="' + key + '"]');
                    const usage = section ? section.dataset.usage : '';
                    if (usage) {
                        window.location.href = '/admin/export/mapel/xls?usage=' + encodeURIComponent(usage);
                    } else {
                        window.location.href = '/admin/export/mapel/xls';
                    }
                });
            });

            createButtons.forEach(function (button) {
                button.addEventListener('click', function () {
                    resetForm();
                });
            });

            editButtons.forEach(function (button) {
                button.addEventListener('click', function () {
                    const subjectId = button.dataset.subjectEdit;
                    const subject = subjectMap[subjectId];

                    if (!subject) {
                        return;
                    }

                    resetForm();
                    idInput.value = subject.id;
                    formTitle.textContent = 'Ubah Mata Pelajaran';
                    submitButton.textContent = 'Perbarui';
                    fillForm(subject);
                    modal?.show();
                });
            });

            deleteButtons.forEach(function (button) {
                button.addEventListener('click', async function () {
                    const subjectId = button.dataset.subjectDelete;
                    const subjectName = button.dataset.subjectName || 'mata pelajaran';

                    if (!subjectId || !window.confirm('Hapus data ' + subjectName + '?')) {
                        return;
                    }

                    const formData = new FormData();
                    formData.append('_method', 'DELETE');

                    const response = await fetch('/admin/mapel/' + subjectId, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                        },
                        body: formData,
                    });

                    if (!response.ok) {
                        showFeedback('Gagal menghapus data mata pelajaran.');

                        return;
                    }

                    window.location.reload();
                });
            });

            form?.addEventListener('submit', async function (event) {
                event.preventDefault();
                formErrors.classList.add('d-none');
                formErrors.innerHTML = '';
                submitButton.disabled = true;

                const formData = new FormData(form);
                const subjectId = idInput.value;
                const endpoint = subjectId ? '/admin/mapel/' + subjectId : '/admin/mapel';

                selectedTeacherIds().forEach(function (teacherId) {
                    formData.append('teacher_ids[]', teacherId);
                });
                selectedClassIds().forEach(function (classId) {
                    formData.append('class_ids[]', classId);
                });

                if (subjectId) {
                    formData.append('_method', 'PUT');
                }

                try {
                    const response = await fetch(endpoint, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                        },
                        body: formData,
                    });

                    const payload = await response.json();

                    if (!response.ok) {
                        renderErrors(payload);
                        submitButton.disabled = false;

                        return;
                    }

                    window.location.reload();
                } catch (error) {
                    renderErrors({ message: 'Terjadi gangguan saat mengirim data. Silakan coba lagi.' });
                    submitButton.disabled = false;
                }
            });
        });
    </script>
@endpush
