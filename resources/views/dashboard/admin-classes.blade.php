@extends('layouts.portal-dashboard')

@section('title', $pageTitle)

@section('content')
    <div class="portal-dashboard-shell portal-directory-shell" data-class-directory>
        @include('dashboard.partials.sidebar', ['menuSections' => $menuSections])

        <main class="portal-dashboard-main portal-directory-main">
            <div class="portal-directory-stack">
                <section class="portal-panel portal-directory-banner">
                    <div class="portal-directory-banner__bar"></div>
                    <div class="portal-directory-banner__copy">
                        <h1>{{ $directoryTitle }}</h1>
                        <p>{{ $directorySubtitle }}</p>
                    </div>
                    <div class="portal-directory-banner__count">{{ $totalClasses }} kelas</div>
                </section>

                <section class="portal-directory-toolbar">
                    <label class="portal-directory-search" for="class-directory-search">
                        <span class="portal-directory-search__icon">
                            @include('dashboard.partials.icon', ['name' => 'search'])
                        </span>
                        <input id="class-directory-search" type="search" placeholder="Pencarian..." data-directory-search>
                    </label>

                    <label class="portal-directory-filter" for="class-directory-filter">
                        <span class="portal-directory-filter__icon">
                            @include('dashboard.partials.icon', ['name' => 'filter'])
                        </span>
                        <select id="class-directory-filter" data-directory-filter>
                            <option value="">Semua tingkat</option>
                            @foreach ($levelOptions as $levelOption)
                                <option value="{{ $levelOption['value'] }}">{{ $levelOption['label'] }}</option>
                            @endforeach
                        </select>
                        <span class="portal-directory-filter__arrow">
                            @include('dashboard.partials.icon', ['name' => 'chevron-down'])
                        </span>
                    </label>

                    <div class="portal-directory-toolbar__actions">
                        <button class="portal-round-action portal-round-action--outline" type="button" aria-label="Unduh semua data kelas" data-directory-export-all>
                            @include('dashboard.partials.icon', ['name' => 'download'])
                        </button>
                        <button class="portal-round-action" type="button" aria-label="Tambah data kelas" data-class-create data-bs-toggle="modal" data-bs-target="#class-directory-modal">
                            @include('dashboard.partials.icon', ['name' => 'plus'])
                        </button>
                    </div>
                </section>

                <div class="portal-directory-feedback d-none" data-directory-feedback></div>

                @forelse ($directoryGroups as $group)
                    <section class="portal-directory-section" data-directory-section data-level="{{ $group['level'] }}" data-section-key="{{ $group['key'] }}">
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
                            <table class="table portal-table portal-directory-table mb-0">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Kelas</th>
                                        <th>Tingkat</th>
                                        <th>Tahun Ajaran</th>
                                        <th>Wali Kelas</th>
                                        <th>Jumlah Siswa</th>
                                        <th>Mapel</th>
                                        <th>Jadwal Ajar</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($group['classes'] as $class)
                                        <tr data-class-row data-level="{{ $class['level'] }}" data-section-key="{{ $group['key'] }}" data-search-text="{{ $class['search_text'] }}">
                                            <td>{{ $loop->iteration }}</td>
                                            <td>
                                                <div class="portal-directory-name">{{ $class['name'] }}</div>
                                                <div class="portal-directory-meta">{{ $class['description'] }}</div>
                                            </td>
                                            <td>{{ $class['level'] }}</td>
                                            <td>{{ $class['academic_year'] }}</td>
                                            <td>{{ $class['homeroom_teacher'] }}</td>
                                            <td>{{ $class['students_count'] }} siswa</td>
                                            <td>{{ $class['subjects_count'] }} mapel</td>
                                            <td>{{ $class['teaching_count'] }} jadwal</td>
                                            <td>
                                                <span class="portal-directory-status {{ $class['status'] === 'Perlu wali' ? 'is-warning' : '' }}">{{ $class['status'] }}</span>
                                            </td>
                                            <td>
                                                <div class="portal-directory-actions">
                                                    <button class="portal-directory-action is-edit" type="button" aria-label="Ubah data {{ $class['name'] }}" data-class-edit="{{ $class['id'] }}">
                                                        @include('dashboard.partials.icon', ['name' => 'edit'])
                                                    </button>
                                                    <button class="portal-directory-action is-delete" type="button" aria-label="Hapus data {{ $class['name'] }}" data-class-delete="{{ $class['id'] }}" data-class-name="{{ $class['name'] }}">
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
                        <h2>Belum ada data kelas</h2>
                        <p>Tambahkan data kelas pertama dari tombol tambah di kanan atas.</p>
                    </section>
                @endforelse

                @if (count($directoryGroups) > 0)
                    <section class="portal-panel portal-directory-empty d-none" data-directory-empty>
                        <h2>Data tidak ditemukan</h2>
                        <p>Coba ubah kata kunci pencarian atau filter tingkat yang sedang dipakai.</p>
                    </section>
                @endif
            </div>
        </main>
    </div>

    <div class="modal fade" id="class-directory-modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content portal-directory-modal">
                <form data-class-form>
                    <div class="modal-header border-0 pb-0">
                        <div>
                            <h2 class="modal-title fs-4 fw-bold" data-class-form-title>Tambah Data Kelas</h2>
                            <p class="text-secondary mb-0">Lengkapi struktur kelas sebelum disimpan.</p>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>

                    <div class="modal-body pt-3">
                        <div class="alert alert-danger d-none" data-class-form-errors></div>
                        <input type="hidden" name="class_id" data-class-id>

                        <div class="row g-3">
                            <div class="col-md-5">
                                <label class="form-label fw-semibold" for="class-name">Nama Kelas</label>
                                <input class="form-control" id="class-name" name="name" type="text" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold" for="class-level">Tingkat</label>
                                <input class="form-control" id="class-level" name="level" type="text" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold" for="class-academic-year">Tahun Ajaran</label>
                                <input class="form-control" id="class-academic-year" name="academic_year" type="text" placeholder="2025/2026" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold" for="class-homeroom-teacher">Wali Kelas</label>
                                <select class="form-select" id="class-homeroom-teacher" name="homeroom_teacher_id">
                                    <option value="">Pilih wali kelas</option>
                                    @foreach ($teacherOptions as $teacherOption)
                                        <option
                                            value="{{ $teacherOption['id'] }}"
                                            data-homeroom-class-id="{{ $teacherOption['homeroom_class_id'] ?? '' }}"
                                            data-homeroom-class-name="{{ $teacherOption['homeroom_class_name'] ?? '' }}"
                                        >
                                            {{ $teacherOption['name'] }}{{ ! empty($teacherOption['homeroom_class_name']) ? ' - Wali '.$teacherOption['homeroom_class_name'] : '' }}
                                        </option>
                                    @endforeach
                                </select>
                                <small class="text-secondary d-block mt-1">Guru yang sudah menjadi wali kelas tidak dapat dipilih untuk kelas lain.</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold" for="class-description">Deskripsi</label>
                                <input class="form-control" id="class-description" name="description" type="text">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold" for="class-student-search">Data Siswa</label>
                                <div class="portal-directory-picker">
                                    <div class="portal-directory-picker__toolbar">
                                        <input class="form-control" id="class-student-search" type="search" placeholder="Cari siswa yang sudah diinput..." data-class-student-search>
                                        <small>Hanya bisa memilih siswa yang sudah ada di data siswa.</small>
                                    </div>
                                    <div class="portal-directory-picker__list">
                                        @forelse ($studentOptions as $studentOption)
                                            <label class="portal-directory-picker__item" data-class-student-item data-search-text="{{ strtolower($studentOption['name'].' '.$studentOption['nik'].' '.$studentOption['current_class']) }}">
                                                <input class="form-check-input" type="checkbox" value="{{ $studentOption['id'] }}" data-class-student-checkbox>
                                                <span>
                                                    <strong>{{ $studentOption['name'] }}</strong>
                                                    <small>NIK {{ $studentOption['nik'] }} | {{ $studentOption['current_class'] }}</small>
                                                </span>
                                            </label>
                                        @empty
                                            <div class="portal-directory-picker__empty">Belum ada data siswa yang dapat dipilih.</div>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold" for="class-subject-search">Mata Pelajaran</label>
                                <div class="portal-directory-picker">
                                    <div class="portal-directory-picker__toolbar">
                                        <input class="form-control" id="class-subject-search" type="search" placeholder="Cari mapel yang sudah diinput..." data-class-subject-search>
                                        <small>Pilih mapel yang dipakai di kelas ini.</small>
                                    </div>
                                    <div class="portal-directory-picker__list">
                                        @forelse ($subjectOptions as $subjectOption)
                                            <label class="portal-directory-picker__item" data-class-subject-item data-search-text="{{ strtolower($subjectOption['code'].' '.$subjectOption['name']) }}">
                                                <input class="form-check-input" type="checkbox" value="{{ $subjectOption['id'] }}" data-class-subject-checkbox>
                                                <span>
                                                    <strong>{{ $subjectOption['name'] }}</strong>
                                                    <small>Kode {{ $subjectOption['code'] }}</small>
                                                </span>
                                            </label>
                                        @empty
                                            <div class="portal-directory-picker__empty">Belum ada data mapel yang dapat dipilih.</div>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary portal-directory-submit" data-class-submit>Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const directory = document.querySelector('[data-class-directory]');

            if (!directory) {
                return;
            }

            const searchInput = directory.querySelector('[data-directory-search]');
            const filterSelect = directory.querySelector('[data-directory-filter]');
            const feedback = directory.querySelector('[data-directory-feedback]');
            const emptyState = directory.querySelector('[data-directory-empty]');
            const sections = Array.from(directory.querySelectorAll('[data-directory-section]'));
            const rows = Array.from(directory.querySelectorAll('[data-class-row]'));
            const exportAllButton = directory.querySelector('[data-directory-export-all]');
            const sectionExportButtons = Array.from(directory.querySelectorAll('[data-directory-export-section]'));
            const createButtons = Array.from(directory.querySelectorAll('[data-class-create]'));
            const editButtons = Array.from(directory.querySelectorAll('[data-class-edit]'));
            const deleteButtons = Array.from(directory.querySelectorAll('[data-class-delete]'));
            const classMap = @json($classPayload);
            const modalElement = document.getElementById('class-directory-modal');
            const modal = modalElement && window.bootstrap ? window.bootstrap.Modal.getOrCreateInstance(modalElement) : null;
            const form = document.querySelector('[data-class-form]');
            const formTitle = document.querySelector('[data-class-form-title]');
            const formErrors = document.querySelector('[data-class-form-errors]');
            const submitButton = document.querySelector('[data-class-submit]');
            const idInput = document.querySelector('[data-class-id]');
            const homeroomTeacherSelect = form?.elements.homeroom_teacher_id;
            const studentSearchInput = document.querySelector('[data-class-student-search]');
            const studentPickerItems = Array.from(document.querySelectorAll('[data-class-student-item]'));
            const studentCheckboxes = Array.from(document.querySelectorAll('[data-class-student-checkbox]'));
            const subjectSearchInput = document.querySelector('[data-class-subject-search]');
            const subjectPickerItems = Array.from(document.querySelectorAll('[data-class-subject-item]'));
            const subjectCheckboxes = Array.from(document.querySelectorAll('[data-class-subject-checkbox]'));
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
                const level = filterSelect?.value || '';
                let visibleRows = 0;

                sections.forEach(function (section) {
                    const sectionRows = Array.from(section.querySelectorAll('[data-class-row]'));
                    let sectionHasVisibleRow = false;

                    sectionRows.forEach(function (row) {
                        const matchesQuery = query === '' || (row.dataset.searchText || '').includes(query);
                        const matchesLevel = level === '' || (row.dataset.level || '') === level;
                        const isVisible = matchesQuery && matchesLevel;

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

                if (query === '' && level === '') {
                    hideFeedback();

                    return;
                }

                showFeedback('Menampilkan ' + visibleRows + ' data kelas sesuai filter aktif.');
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
                    'Nama Kelas',
                    'Deskripsi',
                    'Tingkat',
                    'Tahun Ajaran',
                    'Wali Kelas',
                    'Jumlah Siswa',
                    'Mapel',
                    'Jadwal Ajar',
                    'Status',
                ].map(csvEscape).join(',')];

                visibleRows.forEach(function (row) {
                    const cells = Array.from(row.querySelectorAll('td'));

                    lines.push([
                        cells[0]?.innerText || '',
                        cells[1]?.querySelector('.portal-directory-name')?.innerText || '',
                        cells[1]?.querySelector('.portal-directory-meta')?.innerText || '',
                        cells[2]?.innerText || '',
                        cells[3]?.innerText || '',
                        cells[4]?.innerText || '',
                        cells[5]?.innerText || '',
                        cells[6]?.innerText || '',
                        cells[7]?.innerText || '',
                        cells[8]?.innerText || '',
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

            const updateHomeroomTeacherOptions = function (currentClassId = '') {
                if (!homeroomTeacherSelect) {
                    return;
                }

                Array.from(homeroomTeacherSelect.options).forEach(function (option) {
                    if (!option.value) {
                        return;
                    }

                    const homeroomClassId = option.dataset.homeroomClassId || '';
                    const isUsedByOtherClass = homeroomClassId !== '' && String(homeroomClassId) !== String(currentClassId || '');

                    option.disabled = isUsedByOtherClass;

                    if (isUsedByOtherClass) {
                        option.textContent = option.textContent.replace(/\s*\(tidak tersedia\)$/u, '') + ' (tidak tersedia)';
                    } else {
                        option.textContent = option.textContent.replace(/\s*\(tidak tersedia\)$/u, '');
                    }
                });
            };

            const resetForm = function () {
                if (!form) {
                    return;
                }

                form.reset();
                idInput.value = '';
                formTitle.textContent = 'Tambah Data Kelas';
                submitButton.textContent = 'Simpan';
                submitButton.disabled = false;
                formErrors.classList.add('d-none');
                formErrors.innerHTML = '';
                updateHomeroomTeacherOptions('');
                studentCheckboxes.forEach(function (checkbox) {
                    checkbox.checked = false;
                });
                subjectCheckboxes.forEach(function (checkbox) {
                    checkbox.checked = false;
                });
                studentSearchInput.value = '';
                subjectSearchInput.value = '';
                studentPickerItems.forEach(function (item) {
                    item.classList.remove('d-none');
                });
                subjectPickerItems.forEach(function (item) {
                    item.classList.remove('d-none');
                });
            };

            const fillForm = function (schoolClass) {
                if (!form || !schoolClass) {
                    return;
                }

                form.elements.name.value = schoolClass.name || '';
                form.elements.level.value = schoolClass.level || '';
                form.elements.academic_year.value = schoolClass.academic_year || '';
                updateHomeroomTeacherOptions(schoolClass.id || '');
                form.elements.homeroom_teacher_id.value = schoolClass.homeroom_teacher_id || '';
                form.elements.description.value = schoolClass.description || '';
                const selectedStudentIds = new Set((schoolClass.student_ids || []).map(function (value) {
                    return String(value);
                }));

                studentCheckboxes.forEach(function (checkbox) {
                    checkbox.checked = selectedStudentIds.has(checkbox.value);
                });

                const selectedSubjectIds = new Set((schoolClass.subject_ids || []).map(function (value) {
                    return String(value);
                }));

                subjectCheckboxes.forEach(function (checkbox) {
                    checkbox.checked = selectedSubjectIds.has(checkbox.value);
                });
            };

            const renderErrors = function (payload) {
                if (!formErrors) {
                    return;
                }

                const messages = payload?.errors
                    ? Object.values(payload.errors).flat()
                    : [payload?.message || 'Terjadi kesalahan saat menyimpan data kelas.'];

                formErrors.innerHTML = messages.map(function (message) {
                    return '<div>' + message + '</div>';
                }).join('');
                formErrors.classList.remove('d-none');
            };

            const selectedStudentIds = function () {
                return studentCheckboxes
                    .filter(function (checkbox) { return checkbox.checked; })
                    .map(function (checkbox) { return checkbox.value; });
            };

            const selectedSubjectIds = function () {
                return subjectCheckboxes
                    .filter(function (checkbox) { return checkbox.checked; })
                    .map(function (checkbox) { return checkbox.value; });
            };

            const applyStudentPickerSearch = function () {
                const query = (studentSearchInput?.value || '').trim().toLowerCase();

                studentPickerItems.forEach(function (item) {
                    const matches = query === '' || (item.dataset.searchText || '').includes(query);
                    item.classList.toggle('d-none', !matches);
                });
            };

            const applySubjectPickerSearch = function () {
                const query = (subjectSearchInput?.value || '').trim().toLowerCase();

                subjectPickerItems.forEach(function (item) {
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

            if (studentSearchInput) {
                studentSearchInput.addEventListener('input', applyStudentPickerSearch);
            }

            if (subjectSearchInput) {
                subjectSearchInput.addEventListener('input', applySubjectPickerSearch);
            }

            exportAllButton?.addEventListener('click', function () {
                window.location.href = '/admin/export/kelas/xls';
            });

            sectionExportButtons.forEach(function (button) {
                button.addEventListener('click', function () {
                    const key = button.dataset.directoryExportSection;
                    const section = directory.querySelector('[data-section-key="' + key + '"]');
                    const level = section ? section.dataset.level : '';
                    if (level) {
                        window.location.href = '/admin/export/kelas/xls?level=' + encodeURIComponent(level);
                    } else {
                        window.location.href = '/admin/export/kelas/xls';
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
                    const classId = button.dataset.classEdit;
                    const schoolClass = classMap[classId];

                    if (!schoolClass) {
                        return;
                    }

                    resetForm();
                    idInput.value = schoolClass.id;
                    formTitle.textContent = 'Ubah Data Kelas';
                    submitButton.textContent = 'Perbarui';
                    fillForm(schoolClass);
                    modal?.show();
                });
            });

            deleteButtons.forEach(function (button) {
                button.addEventListener('click', async function () {
                    const classId = button.dataset.classDelete;
                    const className = button.dataset.className || 'kelas';

                    if (!classId || !window.confirm('Hapus data ' + className + '?')) {
                        return;
                    }

                    const formData = new FormData();
                    formData.append('_method', 'DELETE');

                    const response = await fetch('/admin/kelas/' + classId, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                        },
                        body: formData,
                    });

                    if (!response.ok) {
                        showFeedback('Gagal menghapus data kelas.');

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
                const classId = idInput.value;
                const endpoint = classId ? '/admin/kelas/' + classId : '/admin/kelas';

                if (classId) {
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

                    const resolvedClassId = payload?.data?.id || classId;
                    const plottingData = new FormData();
                    plottingData.append('_method', 'PUT');

                    if (form.elements.homeroom_teacher_id.value !== '') {
                        plottingData.append('homeroom_teacher_id', form.elements.homeroom_teacher_id.value);
                    }

                    selectedStudentIds().forEach(function (studentId) {
                        plottingData.append('student_ids[]', studentId);
                    });

                    selectedSubjectIds().forEach(function (subjectId) {
                        plottingData.append('subject_ids[]', subjectId);
                    });

                    const plottingResponse = await fetch('/admin/kelas/' + resolvedClassId + '/ploting', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                        },
                        body: plottingData,
                    });

                    const plottingPayload = await plottingResponse.json();

                    if (!plottingResponse.ok) {
                        renderErrors(plottingPayload);
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
