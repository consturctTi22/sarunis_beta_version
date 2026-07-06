@extends('layouts.portal-dashboard')

@section('title', $pageTitle)

@section('content')
    <div class="portal-dashboard-shell portal-directory-shell" data-teacher-directory>
        @include('dashboard.partials.sidebar', ['menuSections' => $menuSections])

        <main class="portal-dashboard-main portal-directory-main">
            <div class="portal-directory-stack">
                <section class="portal-panel portal-directory-banner">
                    <div class="portal-directory-banner__bar"></div>
                    <div class="portal-directory-banner__copy">
                        <h1>{{ $directoryTitle }}</h1>
                        <p>{{ $directorySubtitle }}</p>
                    </div>
                    <div class="portal-directory-banner__count">{{ $totalTeachers }} guru</div>
                </section>

                <section class="portal-directory-toolbar">
                    <label class="portal-directory-search" for="teacher-directory-search">
                        <span class="portal-directory-search__icon">
                            @include('dashboard.partials.icon', ['name' => 'search'])
                        </span>
                        <input id="teacher-directory-search" type="search" placeholder="Pencarian..." data-directory-search>
                    </label>

                    <label class="portal-directory-filter" for="teacher-directory-filter">
                        <span class="portal-directory-filter__icon">
                            @include('dashboard.partials.icon', ['name' => 'filter'])
                        </span>
                        <select id="teacher-directory-filter" data-directory-filter>
                            <option value="">Semua kategori</option>
                            @foreach ($categoryOptions as $categoryOption)
                                <option value="{{ $categoryOption['key'] }}">{{ $categoryOption['label'] }}</option>
                            @endforeach
                        </select>
                        <span class="portal-directory-filter__arrow">
                            @include('dashboard.partials.icon', ['name' => 'chevron-down'])
                        </span>
                    </label>

                    <div class="portal-directory-toolbar__actions">
                        <a class="portal-round-action portal-round-action--outline" href="{{ url('/admin/import-template/guru') }}" aria-label="Unduh template import guru">
                            @include('dashboard.partials.icon', ['name' => 'report'])
                        </a>
                        <button class="portal-round-action portal-round-action--outline" type="button" aria-label="Unduh semua data guru" data-directory-export-all>
                            @include('dashboard.partials.icon', ['name' => 'download'])
                        </button>
                        <button class="portal-round-action" type="button" aria-label="Tambah data guru" data-teacher-create data-bs-toggle="modal" data-bs-target="#teacher-directory-modal">
                            @include('dashboard.partials.icon', ['name' => 'plus'])
                        </button>
                    </div>
                </section>

                <form class="portal-panel portal-import-strip" action="{{ url('/admin/import/guru') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div>
                        <strong>Import Guru CSV</strong>
                        <span>Header: nip, nik, name, birth_place, birth_date, gender, religion, employment_status, position, join_date, last_education, major, university, phone, address.</span>
                    </div>
                    <input class="form-control" type="file" name="file" accept=".csv,text/csv" required>
                    <button class="btn btn-primary" type="submit">Import</button>
                </form>

                @if (session('import_status'))
                    <div class="portal-directory-feedback" data-import-status>{{ session('import_status') }}</div>
                @endif

                <div class="portal-directory-feedback d-none" data-directory-feedback></div>

                @forelse ($directoryGroups as $group)
                    <section class="portal-directory-section" data-directory-section data-category="{{ $group['category'] }}" data-section-key="{{ $group['key'] }}">
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
                                        <th>Nama Lengkap</th>
                                        <th>NIP / NUPTK</th>
                                        <th>Peran</th>
                                        <th>Jabatan</th>
                                        <th>Mata Pelajaran</th>
                                        <th>Kelas Diampu</th>
                                        <th>No.Hp</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($group['teachers'] as $teacher)
                                        <tr data-teacher-row data-teacher-id="{{ $teacher['id'] }}" data-category="{{ $teacher['category'] }}" data-section-key="{{ $group['key'] }}" data-search-text="{{ $teacher['search_text'] }}">
                                            <td>{{ $loop->iteration }}</td>
                                            <td>
                                                <div class="portal-directory-name">{{ $teacher['name'] }}</div>
                                                <div class="portal-directory-meta">{{ $teacher['employment_status'] }} | {{ $teacher['address'] }}</div>
                                            </td>
                                            <td>{{ $teacher['nip'] }}</td>
                                            <td>{{ $teacher['category_label'] }}</td>
                                            <td>{{ $teacher['position'] }}</td>
                                            <td>{{ $teacher['subject_label'] }}</td>
                                            <td>{{ $teacher['teaching_class_label'] }}</td>
                                            <td>{{ $teacher['phone'] }}</td>
                                            <td>
                                                <div class="portal-directory-actions">
                                                    <button class="portal-directory-action is-view" type="button" aria-label="Lihat detail guru {{ $teacher['name'] }}" data-teacher-view="{{ $teacher['id'] }}">
                                                        @include('dashboard.partials.icon', ['name' => 'eye'])
                                                    </button>
                                                    <button class="portal-directory-action is-edit" type="button" aria-label="Ubah data {{ $teacher['name'] }}" data-teacher-edit="{{ $teacher['id'] }}">
                                                        @include('dashboard.partials.icon', ['name' => 'edit'])
                                                    </button>
                                                    <button class="portal-directory-action is-delete" type="button" aria-label="Hapus data {{ $teacher['name'] }}" data-teacher-delete="{{ $teacher['id'] }}" data-teacher-name="{{ $teacher['name'] }}">
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
                        <h2>Belum ada data guru</h2>
                        <p>Tambahkan data guru pertama dari tombol tambah di kanan atas.</p>
                    </section>
                @endforelse

                @if (count($directoryGroups) > 0)
                    <section class="portal-panel portal-directory-empty d-none" data-directory-empty>
                        <h2>Data tidak ditemukan</h2>
                        <p>Coba ubah kata kunci pencarian atau filter kategori yang sedang dipakai.</p>
                    </section>
                @endif
            </div>
        </main>
    </div>

    <div class="modal fade" id="teacher-directory-modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-fullscreen-lg-down portal-student-modal__dialog">
            <div class="modal-content portal-directory-modal portal-student-modal">
                <form class="portal-student-form" data-teacher-form>
                    <div class="modal-header border-0 pb-0">
                        <div>
                            <h2 class="modal-title fs-4 fw-bold" data-teacher-form-title>Tambah Data Guru</h2>
                            <p class="text-secondary mb-0">Lengkapi biodata guru. Status peran ditentukan otomatis dari plotting mapel, jadwal, dan walikelas.</p>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>

                    <div class="modal-body portal-student-modal__body pt-3">
                        <div class="alert alert-danger d-none" data-teacher-form-errors></div>
                        <input type="hidden" name="teacher_id" data-teacher-id>

                        <div class="portal-student-form__stack">
                            <section class="portal-student-form__section">
                                <div class="portal-student-form__section-head">
                                    <div>
                                        <h3>Data Utama</h3>
                                        <p>Identitas dasar guru dan data kontak yang dipakai di daftar utama.</p>
                                    </div>
                                </div>

                                <div class="portal-student-form__grid">
                                    <div class="portal-student-form__field portal-student-form__field--span-6">
                                        <label class="form-label fw-semibold" for="teacher-name">Nama Lengkap</label>
                                        <input class="form-control" id="teacher-name" name="name" type="text" required>
                                    </div>
                                    <div class="portal-student-form__field portal-student-form__field--span-3">
                                        <label class="form-label fw-semibold" for="teacher-nik">NIK</label>
                                        <input class="form-control" id="teacher-nik" name="nik" type="text">
                                    </div>
                                    <div class="portal-student-form__field portal-student-form__field--span-3">
                                        <label class="form-label fw-semibold" for="teacher-nip">NIP / NUPTK</label>
                                        <input class="form-control" id="teacher-nip" name="nip" type="text" required>
                                    </div>
                                    <div class="portal-student-form__field portal-student-form__field--span-4">
                                        <label class="form-label fw-semibold" for="teacher-gender">Jenis Kelamin</label>
                                        <select class="form-select" id="teacher-gender" name="gender">
                                            <option value="">Pilih jenis kelamin</option>
                                            <option value="L">Laki-laki</option>
                                            <option value="P">Perempuan</option>
                                        </select>
                                    </div>
                                    <div class="portal-student-form__field portal-student-form__field--span-4">
                                        <label class="form-label fw-semibold" for="teacher-birth-place">Tempat Lahir</label>
                                        <input class="form-control" id="teacher-birth-place" name="birth_place" type="text">
                                    </div>
                                    <div class="portal-student-form__field portal-student-form__field--span-4">
                                        <label class="form-label fw-semibold" for="teacher-birth-date">Tanggal Lahir</label>
                                        <input class="form-control" id="teacher-birth-date" name="birth_date" type="date">
                                    </div>
                                    <div class="portal-student-form__field portal-student-form__field--span-4">
                                        <label class="form-label fw-semibold" for="teacher-religion">Agama</label>
                                        <select class="form-select" id="teacher-religion" name="religion">
                                            <option value="">Pilih agama</option>
                                            <option value="Islam">Islam</option>
                                            <option value="Kristen">Kristen</option>
                                            <option value="Katolik">Katolik</option>
                                            <option value="Hindu">Hindu</option>
                                            <option value="Buddha">Buddha</option>
                                            <option value="Khonghucu">Khonghucu</option>
                                        </select>
                                    </div>
                                    <div class="portal-student-form__field portal-student-form__field--span-4">
                                        <label class="form-label fw-semibold" for="teacher-phone">No. HP</label>
                                        <input class="form-control" id="teacher-phone" name="phone" type="text">
                                    </div>
                                    <div class="portal-student-form__field portal-student-form__field--span-4">
                                        <label class="form-label fw-semibold" for="teacher-join-date">Tanggal Masuk</label>
                                        <input class="form-control" id="teacher-join-date" name="join_date" type="date">
                                    </div>
                                    <div class="portal-student-form__field portal-student-form__field--span-6">
                                        <label class="form-label fw-semibold" for="teacher-employment-status">Status Guru</label>
                                        <input class="form-control" id="teacher-employment-status" name="employment_status" type="text" placeholder="Contoh: Guru Tetap, Honorer, PPPK">
                                    </div>
                                    <div class="portal-student-form__field portal-student-form__field--span-6">
                                        <label class="form-label fw-semibold" for="teacher-position">Jabatan</label>
                                        <input class="form-control" id="teacher-position" name="position" type="text" placeholder="Contoh: Guru BK, Wakil Kepala Sekolah">
                                    </div>
                                    <div class="portal-student-form__field portal-student-form__field--span-12">
                                        <label class="form-label fw-semibold" for="teacher-address">Alamat</label>
                                        <textarea class="form-control" id="teacher-address" name="address" rows="2"></textarea>
                                    </div>
                                </div>
                            </section>

                            <section class="portal-student-form__section">
                                <div class="portal-student-form__section-head">
                                    <div>
                                        <h3>Pendidikan</h3>
                                        <p>Lengkapi latar pendidikan terakhir agar data guru lebih mudah dikelompokkan.</p>
                                    </div>
                                </div>

                                <div class="portal-student-form__grid">
                                    <div class="portal-student-form__field portal-student-form__field--span-4">
                                        <label class="form-label fw-semibold" for="teacher-last-education">Pendidikan Terakhir</label>
                                        <input class="form-control" id="teacher-last-education" name="last_education" type="text">
                                    </div>
                                    <div class="portal-student-form__field portal-student-form__field--span-4">
                                        <label class="form-label fw-semibold" for="teacher-major">Jurusan</label>
                                        <input class="form-control" id="teacher-major" name="major" type="text">
                                    </div>
                                    <div class="portal-student-form__field portal-student-form__field--span-4">
                                        <label class="form-label fw-semibold" for="teacher-university">Universitas</label>
                                        <input class="form-control" id="teacher-university" name="university" type="text">
                                    </div>
                                </div>
                            </section>

                            <section class="portal-student-form__section">
                                <div class="portal-student-form__section-head">
                                    <div>
                                        <h3>Status Peran Otomatis</h3>
                                        <p>Tipe guru tidak dipilih manual. Status berubah otomatis sesuai mapel, jadwal ajar, dan kelas perwalian yang tertaut.</p>
                                    </div>
                                </div>

                                <div class="portal-student-form__grid">
                                    <div class="portal-student-form__field portal-student-form__field--span-3">
                                        <label class="form-label fw-semibold">Status Saat Ini</label>
                                        <div class="form-control d-flex align-items-center" data-teacher-role-status>Guru</div>
                                    </div>
                                    <div class="portal-student-form__field portal-student-form__field--span-3">
                                        <label class="form-label fw-semibold">Mata Pelajaran</label>
                                        <div class="form-control d-flex align-items-center" data-teacher-role-mapel>Belum ada mapel</div>
                                    </div>
                                    <div class="portal-student-form__field portal-student-form__field--span-3">
                                        <label class="form-label fw-semibold">Kelas Yang Diampu</label>
                                        <div class="form-control d-flex align-items-center" data-teacher-role-classes>Belum ada kelas</div>
                                    </div>
                                    <div class="portal-student-form__field portal-student-form__field--span-3">
                                        <label class="form-label fw-semibold">Kelas Perwalian</label>
                                        <div class="form-control d-flex align-items-center" data-teacher-role-homeroom>Belum ada perwalian</div>
                                    </div>
                                </div>
                            </section>
                        </div>
                    </div>

                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary portal-directory-submit" data-teacher-submit>Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="teacher-view-modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-fullscreen-lg-down portal-student-view-modal__dialog">
            <div class="modal-content portal-directory-modal portal-student-view-modal">
                <div class="modal-header border-0 pb-0">
                    <div>
                        <h2 class="modal-title fs-4 fw-bold">Data Guru</h2>
                        <p class="text-secondary mb-0">Lihat biodata guru, status peran, dan ringkasan data mengajar.</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>

                <div class="modal-body portal-student-view-modal__body pt-3" data-teacher-view-content></div>

                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const directory = document.querySelector('[data-teacher-directory]');

            if (!directory) {
                return;
            }

            const searchInput = directory.querySelector('[data-directory-search]');
            const filterSelect = directory.querySelector('[data-directory-filter]');
            const feedback = directory.querySelector('[data-directory-feedback]');
            const emptyState = directory.querySelector('[data-directory-empty]');
            const sections = Array.from(directory.querySelectorAll('[data-directory-section]'));
            const rows = Array.from(directory.querySelectorAll('[data-teacher-row]'));
            const exportAllButton = directory.querySelector('[data-directory-export-all]');
            const sectionExportButtons = Array.from(directory.querySelectorAll('[data-directory-export-section]'));
            const createButtons = Array.from(directory.querySelectorAll('[data-teacher-create]'));
            const viewButtons = Array.from(directory.querySelectorAll('[data-teacher-view]'));
            const editButtons = Array.from(directory.querySelectorAll('[data-teacher-edit]'));
            const deleteButtons = Array.from(directory.querySelectorAll('[data-teacher-delete]'));
            const teacherMap = @json($teacherPayload);
            const modalElement = document.getElementById('teacher-directory-modal');
            const modal = modalElement && window.bootstrap ? window.bootstrap.Modal.getOrCreateInstance(modalElement) : null;
            const viewModalElement = document.getElementById('teacher-view-modal');
            const viewModal = viewModalElement && window.bootstrap ? window.bootstrap.Modal.getOrCreateInstance(viewModalElement) : null;
            const form = document.querySelector('[data-teacher-form]');
            const formTitle = document.querySelector('[data-teacher-form-title]');
            const formErrors = document.querySelector('[data-teacher-form-errors]');
            const submitButton = document.querySelector('[data-teacher-submit]');
            const idInput = document.querySelector('[data-teacher-id]');
            const roleStatusField = document.querySelector('[data-teacher-role-status]');
            const roleMapelField = document.querySelector('[data-teacher-role-mapel]');
            const roleClassesField = document.querySelector('[data-teacher-role-classes]');
            const roleHomeroomField = document.querySelector('[data-teacher-role-homeroom]');
            const viewContent = document.querySelector('[data-teacher-view-content]');
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

            const escapeHtml = function (value) {
                return String(value ?? '')
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;')
                    .replace(/'/g, '&#039;');
            };

            const normalizeDisplayValue = function (value, fallback = '-') {
                const normalized = String(value ?? '').trim();

                return normalized === '' ? fallback : normalized;
            };

            const buildInitials = function (value) {
                const parts = String(value ?? '')
                    .trim()
                    .split(/\s+/)
                    .filter(Boolean)
                    .slice(0, 2);

                if (parts.length === 0) {
                    return 'GR';
                }

                return parts.map(function (part) {
                    return part.charAt(0).toUpperCase();
                }).join('');
            };

            const setTeacherSummary = function (teacher) {
                const payload = teacher || {};

                if (roleStatusField) {
                    roleStatusField.textContent = payload.status || 'Guru';
                }

                if (roleMapelField) {
                    roleMapelField.textContent = payload.subject_label || 'Belum ada mapel';
                }

                if (roleClassesField) {
                    roleClassesField.textContent = payload.teaching_class_label || 'Belum ada kelas';
                }

                if (roleHomeroomField) {
                    roleHomeroomField.textContent = payload.homeroom_classes || 'Belum ada perwalian';
                }
            };

            const applyFilters = function () {
                const query = (searchInput?.value || '').trim().toLowerCase();
                const category = filterSelect?.value || '';
                let visibleRows = 0;

                sections.forEach(function (section) {
                    const sectionRows = Array.from(section.querySelectorAll('[data-teacher-row]'));
                    let sectionHasVisibleRow = false;

                    sectionRows.forEach(function (row) {
                        const matchesQuery = query === '' || (row.dataset.searchText || '').includes(query);
                        const matchesCategory = category === '' || (row.dataset.category || '') === category;
                        const isVisible = matchesQuery && matchesCategory;

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

                if (query === '' && category === '') {
                    hideFeedback();

                    return;
                }

                showFeedback('Menampilkan ' + visibleRows + ' data guru sesuai filter aktif.');
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
                    'Nama Lengkap',
                    'Status Guru',
                    'Alamat',
                    'NIP / NUPTK',
                    'Peran',
                    'Jabatan',
                    'Mata Pelajaran',
                    'Kelas Diampu',
                    'No.Hp',
                ].map(csvEscape).join(',')];

                visibleRows.forEach(function (row) {
                    const teacher = teacherMap[row.dataset.teacherId || ''];

                    lines.push([
                        row.querySelector('td')?.innerText || '',
                        teacher?.name || '',
                        teacher?.employment_status || '',
                        teacher?.address || '',
                        teacher?.nip || '',
                        teacher?.category_label || '',
                        teacher?.position || '',
                        teacher?.subject_label || '',
                        teacher?.teaching_class_label || '',
                        teacher?.phone || '',
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
                formTitle.textContent = 'Tambah Data Guru';
                submitButton.textContent = 'Simpan';
                submitButton.disabled = false;
                formErrors.classList.add('d-none');
                formErrors.innerHTML = '';
                setTeacherSummary({
                    status: 'Guru',
                    subject_label: 'Belum ada mapel',
                    teaching_class_label: 'Belum ada kelas',
                    homeroom_classes: 'Belum ada perwalian',
                });
            };

            const setFormValue = function (name, value) {
                if (!form) {
                    return;
                }

                const field = form.querySelector('[name="' + name + '"]');

                if (!field) {
                    return;
                }

                field.value = value || '';
            };

            const fillForm = function (teacher) {
                if (!form || !teacher) {
                    return;
                }

                setFormValue('name', teacher.name);
                setFormValue('nik', teacher.nik);
                setFormValue('nip', teacher.nip);
                setFormValue('gender', teacher.gender);
                setFormValue('birth_place', teacher.birth_place);
                setFormValue('birth_date', teacher.birth_date);
                setFormValue('religion', teacher.religion);
                setFormValue('phone', teacher.phone);
                setFormValue('employment_status', teacher.employment_status);
                setFormValue('position', teacher.position);
                setFormValue('join_date', teacher.join_date);
                setFormValue('last_education', teacher.last_education);
                setFormValue('major', teacher.major);
                setFormValue('university', teacher.university);
                setFormValue('address', teacher.address);
                setTeacherSummary(teacher);
            };

            const openViewModal = function (teacher) {
                if (!teacher || !viewContent) {
                    return;
                }

                const photoMarkup = teacher.photo_url
                    ? '<img class="portal-student-view__avatar" src="' + escapeHtml(teacher.photo_url) + '" alt="' + escapeHtml(teacher.name) + '">'
                    : '<div class="portal-student-view__avatar portal-student-view__avatar--fallback">' + escapeHtml(buildInitials(teacher.name)) + '</div>';

                const buildItems = function (items) {
                    return items.map(function (item) {
                        return '<div class="portal-student-view__item"><span>' + escapeHtml(item.label) + '</span><strong>' + escapeHtml(normalizeDisplayValue(item.value)) + '</strong></div>';
                    }).join('');
                };

                viewContent.innerHTML = [
                    '<section class="portal-student-view__hero">',
                        '<div class="portal-student-view__hero-media">' + photoMarkup + '</div>',
                        '<div class="portal-student-view__hero-copy">',
                            '<span class="portal-student-view__eyebrow">Data guru</span>',
                            '<h3>' + escapeHtml(teacher.name) + '</h3>',
                            '<div class="portal-student-view__hero-meta">',
                                '<span>' + escapeHtml(normalizeDisplayValue(teacher.category_label, 'Guru')) + '</span>',
                                '<span>' + escapeHtml(normalizeDisplayValue(teacher.position)) + '</span>',
                            '</div>',
                        '</div>',
                    '</section>',
                    '<div class="portal-student-view__grid">',
                        '<section class="portal-student-view__card"><h4>Identitas</h4><div class="portal-student-view__items">' + buildItems([
                            { label: 'NIK', value: teacher.nik },
                            { label: 'NIP / NUPTK', value: teacher.nip },
                            { label: 'Jenis Kelamin', value: teacher.gender_label },
                            { label: 'Tempat Lahir', value: teacher.birth_place },
                            { label: 'Tanggal Lahir', value: teacher.birth_date_label },
                            { label: 'Agama', value: teacher.religion },
                        ]) + '</div></section>',
                        '<section class="portal-student-view__card"><h4>Kepegawaian</h4><div class="portal-student-view__items">' + buildItems([
                            { label: 'Status Peran', value: teacher.status },
                            { label: 'Status Guru', value: teacher.employment_status },
                            { label: 'Jabatan', value: teacher.position },
                            { label: 'Tanggal Masuk', value: teacher.join_date_label },
                            { label: 'No. HP', value: teacher.phone },
                            { label: 'Alamat', value: teacher.address },
                        ]) + '</div></section>',
                        '<section class="portal-student-view__card"><h4>Pendidikan</h4><div class="portal-student-view__items">' + buildItems([
                            { label: 'Pendidikan Terakhir', value: teacher.last_education },
                            { label: 'Jurusan', value: teacher.major },
                            { label: 'Universitas', value: teacher.university },
                        ]) + '</div></section>',
                        '<section class="portal-student-view__card"><h4>Data Mengajar</h4><div class="portal-student-view__items">' + buildItems([
                            { label: 'Mata Pelajaran', value: teacher.subject_label },
                            { label: 'Kelas Yang Diampu', value: teacher.teaching_class_label },
                            { label: 'Kelas Perwalian', value: teacher.homeroom_classes },
                            { label: 'Total Jadwal', value: teacher.teaching_count ? teacher.teaching_count + ' jadwal' : '-' },
                        ]) + '</div></section>',
                    '</div>',
                ].join('');

                viewModal?.show();
            };

            const renderErrors = function (payload) {
                if (!formErrors) {
                    return;
                }

                const messages = payload?.errors
                    ? Object.values(payload.errors).flat()
                    : [payload?.message || 'Terjadi kesalahan saat menyimpan data guru.'];

                formErrors.innerHTML = messages.map(function (message) {
                    return '<div>' + message + '</div>';
                }).join('');
                formErrors.classList.remove('d-none');
            };

            modalElement?.addEventListener('hidden.bs.modal', function () {
                resetForm();
            });

            if (searchInput) {
                searchInput.addEventListener('input', applyFilters);
            }

            if (filterSelect) {
                filterSelect.addEventListener('change', applyFilters);
            }

            exportAllButton?.addEventListener('click', function () {
                window.location.href = '/admin/export/guru/xls';
            });

            sectionExportButtons.forEach(function (button) {
                button.addEventListener('click', function () {
                    const key = button.dataset.directoryExportSection;
                    const section = directory.querySelector('[data-section-key="' + key + '"]');
                    const category = section ? section.dataset.category : '';
                    if (category) {
                        window.location.href = '/admin/export/guru/xls?category=' + category;
                    } else {
                        window.location.href = '/admin/export/guru/xls';
                    }
                });
            });

            createButtons.forEach(function (button) {
                button.addEventListener('click', function () {
                    resetForm();
                });
            });

            viewButtons.forEach(function (button) {
                button.addEventListener('click', function () {
                    const teacherId = button.dataset.teacherView;
                    const teacher = teacherMap[teacherId];

                    if (!teacher) {
                        return;
                    }

                    openViewModal(teacher);
                });
            });

            editButtons.forEach(function (button) {
                button.addEventListener('click', function () {
                    const teacherId = button.dataset.teacherEdit;
                    const teacher = teacherMap[teacherId];

                    if (!teacher) {
                        return;
                    }

                    resetForm();
                    idInput.value = teacher.id;
                    formTitle.textContent = 'Ubah Data Guru';
                    submitButton.textContent = 'Perbarui';
                    fillForm(teacher);
                    modal?.show();
                });
            });

            deleteButtons.forEach(function (button) {
                button.addEventListener('click', async function () {
                    const teacherId = button.dataset.teacherDelete;
                    const teacherName = button.dataset.teacherName || 'guru';

                    if (!teacherId || !window.confirm('Hapus data ' + teacherName + '?')) {
                        return;
                    }

                    const formData = new FormData();
                    formData.append('_method', 'DELETE');

                    const response = await fetch('/admin/guru/' + teacherId, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                        },
                        body: formData,
                    });

                    if (!response.ok) {
                        showFeedback('Gagal menghapus data guru.');

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
                const teacherId = idInput.value;
                const endpoint = teacherId ? '/admin/guru/' + teacherId : '/admin/guru';

                if (teacherId) {
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
