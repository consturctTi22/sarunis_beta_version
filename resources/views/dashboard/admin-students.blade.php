@extends('layouts.portal-dashboard')

@section('title', $pageTitle)

@section('content')
    <div class="portal-dashboard-shell portal-directory-shell" data-student-directory>
        @include('dashboard.partials.sidebar', ['menuSections' => $menuSections])

        <main class="portal-dashboard-main portal-directory-main">
            <div class="portal-directory-stack">
                <section class="portal-panel portal-directory-banner">
                    <div class="portal-directory-banner__bar"></div>
                    <div class="portal-directory-banner__copy">
                        <h1>{{ $directoryTitle }}</h1>
                        <p>{{ $directorySubtitle }}</p>
                    </div>
                    <div class="portal-directory-banner__count">{{ $totalStudents }} siswa</div>
                </section>

                <section class="portal-directory-toolbar">
                    <label class="portal-directory-search" for="student-directory-search">
                        <span class="portal-directory-search__icon">
                            @include('dashboard.partials.icon', ['name' => 'search'])
                        </span>
                        <input id="student-directory-search" type="search" placeholder="Pencarian..." data-directory-search>
                    </label>

                    <label class="portal-directory-filter" for="student-directory-filter">
                        <span class="portal-directory-filter__icon">
                            @include('dashboard.partials.icon', ['name' => 'filter'])
                        </span>
                        <select id="student-directory-filter" data-directory-filter>
                            <option value="">Semua kelas</option>
                            @foreach ($classOptions as $classOption)
                                <option value="{{ $classOption['id'] }}">{{ $classOption['name'] }}</option>
                            @endforeach
                        </select>
                        <span class="portal-directory-filter__arrow">
                            @include('dashboard.partials.icon', ['name' => 'chevron-down'])
                        </span>
                    </label>

                    <div class="portal-directory-toolbar__actions">
                        <a class="portal-round-action portal-round-action--outline" href="{{ url('/admin/import-template/siswa') }}" aria-label="Unduh template import siswa">
                            @include('dashboard.partials.icon', ['name' => 'report'])
                        </a>
                        <button class="portal-round-action portal-round-action--outline" type="button" aria-label="Unduh semua data siswa" data-directory-export-all>
                            @include('dashboard.partials.icon', ['name' => 'download'])
                        </button>
                        <button class="portal-round-action" type="button" aria-label="Tambah data siswa" data-student-create data-bs-toggle="modal" data-bs-target="#student-directory-modal">
                            @include('dashboard.partials.icon', ['name' => 'plus'])
                        </button>
                    </div>
                </section>

                <form class="portal-panel portal-import-strip" action="{{ url('/admin/import/siswa') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div>
                        <strong>Import Siswa CSV</strong>
                        <span>Header: nik, nisn, name, gender, birth_date, phone, address, school_class_id atau class_name.</span>
                    </div>
                    <input class="form-control" type="file" name="file" accept=".csv,text/csv" required>
                    <button class="btn btn-primary" type="submit">Import</button>
                </form>

                @if (session('import_status'))
                    <div class="portal-directory-feedback" data-import-status>{{ session('import_status') }}</div>
                @endif

                <div class="portal-directory-feedback d-none" data-directory-feedback></div>

                @forelse ($directoryGroups as $group)
                    <section class="portal-directory-section" data-directory-section data-class-id="{{ $group['class_id'] ?? '' }}" data-section-key="{{ $group['key'] }}">
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
                                        <th>NISN</th>
                                        <th>Tanggal Lahir</th>
                                        <th>Jenis Kelamin</th>
                                        <th>Kelas</th>
                                        <th>Walikelas</th>
                                        <th>No.Hp Wali</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($group['students'] as $student)
                                        <tr data-student-row data-class-id="{{ $student['class_id'] ?? '' }}" data-section-key="{{ $group['key'] }}" data-search-text="{{ $student['search_text'] }}">
                                            <td>{{ $loop->iteration }}</td>
                                            <td>
                                                <div class="portal-directory-name">{{ $student['name'] }}</div>
                                                <div class="portal-directory-meta">NIK {{ $student['nik'] }}</div>
                                            </td>
                                            <td>{{ $student['nisn'] }}</td>
                                            <td>{{ $student['birth_date_label'] }}</td>
                                            <td>{{ $student['gender_label'] }}</td>
                                            <td>{{ $student['class_name'] }}</td>
                                            <td>{{ $student['homeroom_teacher'] }}</td>
                                            <td>{{ $student['homeroom_phone'] }}</td>
                                            <td>
                                                <span class="portal-directory-status">{{ $student['status'] }}</span>
                                            </td>
                                            <td>
                                                <div class="portal-directory-actions">
                                                    <button class="portal-directory-action is-view" type="button" aria-label="Lihat detail siswa {{ $student['name'] }}" data-student-view="{{ $student['id'] }}">
                                                        @include('dashboard.partials.icon', ['name' => 'eye'])
                                                    </button>
                                                    <button class="portal-directory-action is-edit" type="button" aria-label="Ubah data {{ $student['name'] }}" data-student-edit="{{ $student['id'] }}">
                                                        @include('dashboard.partials.icon', ['name' => 'edit'])
                                                    </button>
                                                    <button class="portal-directory-action is-delete" type="button" aria-label="Hapus data {{ $student['name'] }}" data-student-delete="{{ $student['id'] }}" data-student-name="{{ $student['name'] }}">
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
                        <h2>Belum ada data siswa</h2>
                        <p>Tambahkan data siswa pertama dari tombol tambah di kanan atas.</p>
                    </section>
                @endforelse

                @if (count($directoryGroups) > 0)
                    <section class="portal-panel portal-directory-empty d-none" data-directory-empty>
                        <h2>Data tidak ditemukan</h2>
                        <p>Coba ubah kata kunci pencarian atau filter kelas yang sedang dipakai.</p>
                    </section>
                @endif
            </div>
        </main>
    </div>

    <div class="modal fade" id="student-directory-modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-fullscreen-lg-down portal-student-modal__dialog">
            <div class="modal-content portal-directory-modal portal-student-modal">
                <form class="portal-student-form" data-student-form enctype="multipart/form-data">
                    <div class="modal-header border-0 pb-0">
                        <div>
                            <h2 class="modal-title fs-4 fw-bold" data-student-form-title>Tambah Data Siswa</h2>
                            <p class="text-secondary mb-0">Lengkapi data utama dan detail siswa sebelum disimpan.</p>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>

                    <div class="modal-body portal-student-modal__body pt-3">
                        <div class="alert alert-danger d-none" data-student-form-errors></div>
                        <input type="hidden" name="student_id" data-student-id>

                        <div class="portal-student-form__stack">
                            <section class="portal-student-form__section">
                                <div class="portal-student-form__section-head">
                                    <div>
                                        <h3>Data Utama</h3>
                                        <p>Informasi identitas inti siswa yang tampil di daftar utama.</p>
                                    </div>
                                </div>

                                <div class="portal-student-form__grid">
                                    <div class="portal-student-form__field portal-student-form__field--span-12">
                                        <div class="portal-student-photo-field">
                                            <div class="portal-student-photo-field__preview" data-student-photo-preview-wrapper>
                                                <img class="portal-student-photo-field__image d-none" alt="Preview foto siswa" data-student-photo-preview-image>
                                                <div class="portal-student-photo-field__fallback" data-student-photo-preview-fallback>FS</div>
                                            </div>
                                            <div class="portal-student-photo-field__body">
                                                <div>
                                                    <label class="form-label fw-semibold" for="student-photo">Foto Siswa</label>
                                                    <p class="portal-student-photo-field__help">Unggah JPG, PNG, atau WEBP dengan ukuran maksimal 2 MB.</p>
                                                </div>
                                                <input class="form-control" id="student-photo" name="photo" type="file" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" data-student-photo-input>
                                                <div class="form-check portal-student-photo-field__remove">
                                                    <input class="form-check-input" id="student-remove-photo" name="remove_photo" type="checkbox" value="1" data-student-remove-photo>
                                                    <label class="form-check-label" for="student-remove-photo">Hapus foto lama saat disimpan</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="portal-student-form__field portal-student-form__field--span-6">
                                        <label class="form-label fw-semibold" for="student-name">Nama Lengkap</label>
                                        <input class="form-control" id="student-name" name="name" type="text" required>
                                    </div>
                                    <div class="portal-student-form__field portal-student-form__field--span-3">
                                        <label class="form-label fw-semibold" for="student-nik">NIK</label>
                                        <input class="form-control" id="student-nik" name="nik" type="text" required>
                                    </div>
                                    <div class="portal-student-form__field portal-student-form__field--span-3">
                                        <label class="form-label fw-semibold" for="student-nisn">NISN</label>
                                        <input class="form-control" id="student-nisn" name="nisn" type="text">
                                    </div>
                                    <div class="portal-student-form__field portal-student-form__field--span-4">
                                        <label class="form-label fw-semibold" for="student-class">Kelas</label>
                                        <select class="form-select" id="student-class" name="school_class_id">
                                            <option value="">Pilih kelas</option>
                                            @foreach ($classOptions as $classOption)
                                                <option value="{{ $classOption['id'] }}">{{ $classOption['name'] }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="portal-student-form__field portal-student-form__field--span-4">
                                        <label class="form-label fw-semibold" for="student-gender">Jenis Kelamin</label>
                                        <select class="form-select" id="student-gender" name="gender">
                                            <option value="">Pilih jenis kelamin</option>
                                            <option value="L">Laki-laki</option>
                                            <option value="P">Perempuan</option>
                                        </select>
                                    </div>
                                    <div class="portal-student-form__field portal-student-form__field--span-4">
                                        <label class="form-label fw-semibold" for="student-birth-date">Tanggal Lahir</label>
                                        <input class="form-control" id="student-birth-date" name="birth_date" type="date">
                                    </div>
                                </div>
                            </section>

                            <section class="portal-student-form__section">
                                <div class="portal-student-form__section-head">
                                    <div>
                                        <h3>Detail Siswa</h3>
                                        <p>Lengkapi biodata siswa agar data administrasi lebih rapi dan mudah dicari.</p>
                                    </div>
                                </div>

                                <div class="portal-student-form__grid">
                                    <div class="portal-student-form__field portal-student-form__field--span-4">
                                        <label class="form-label fw-semibold" for="student-birth-place">Tempat Lahir</label>
                                        <input class="form-control" id="student-birth-place" name="detail_siswa[birth_place]" type="text">
                                    </div>
                                    <div class="portal-student-form__field portal-student-form__field--span-4">
                                        <label class="form-label fw-semibold" for="student-religion">Agama</label>
                                        <input class="form-control" id="student-religion" name="detail_siswa[religion]" type="text">
                                    </div>
                                    <div class="portal-student-form__field portal-student-form__field--span-4">
                                        <label class="form-label fw-semibold" for="student-phone">No. HP Siswa</label>
                                        <input class="form-control" id="student-phone" name="phone" type="text">
                                    </div>
                                    <div class="portal-student-form__field portal-student-form__field--span-4">
                                        <label class="form-label fw-semibold" for="student-origin-school">Pendidikan Asal</label>
                                        <input class="form-control" id="student-origin-school" name="detail_siswa[previous_school]" type="text">
                                    </div>
                                    <div class="portal-student-form__field portal-student-form__field--span-8">
                                        <label class="form-label fw-semibold" for="student-address">Alamat Ringkas</label>
                                        <textarea class="form-control" id="student-address" name="address" rows="2"></textarea>
                                    </div>
                                </div>
                            </section>

                            <section class="portal-student-form__section">
                                <div class="portal-student-form__section-head">
                                    <div>
                                        <h3>Alamat Siswa</h3>
                                        <p>Gunakan alamat terstruktur agar lebih mudah dipakai untuk laporan dan pencarian wilayah.</p>
                                    </div>
                                </div>

                                <div class="portal-student-form__grid">
                                    <div class="portal-student-form__field portal-student-form__field--span-12">
                                        <label class="form-label fw-semibold" for="student-address-street">Jalan</label>
                                        <textarea class="form-control" id="student-address-street" name="detail_siswa[address_street]" rows="2"></textarea>
                                    </div>
                                    <div class="portal-student-form__field portal-student-form__field--span-3">
                                        <label class="form-label fw-semibold" for="student-address-village">Kelurahan</label>
                                        <input class="form-control" id="student-address-village" name="detail_siswa[address_village]" type="text">
                                    </div>
                                    <div class="portal-student-form__field portal-student-form__field--span-3">
                                        <label class="form-label fw-semibold" for="student-address-district">Kecamatan</label>
                                        <input class="form-control" id="student-address-district" name="detail_siswa[address_district]" type="text">
                                    </div>
                                    <div class="portal-student-form__field portal-student-form__field--span-3">
                                        <label class="form-label fw-semibold" for="student-address-province">Propinsi</label>
                                        <input class="form-control" id="student-address-province" name="detail_siswa[address_province]" type="text">
                                    </div>
                                    <div class="portal-student-form__field portal-student-form__field--span-3">
                                        <label class="form-label fw-semibold" for="student-address-city">Kota</label>
                                        <input class="form-control" id="student-address-city" name="detail_siswa[address_city]" type="text">
                                    </div>
                                </div>
                            </section>

                            <section class="portal-student-form__section">
                                <div class="portal-student-form__section-head">
                                    <div>
                                        <h3>Data Orang Tua</h3>
                                        <p>Masukkan identitas ayah dan ibu dalam satu blok agar lebih ringkas saat diisi.</p>
                                    </div>
                                </div>

                                <div class="portal-student-form__grid">
                                    <div class="portal-student-form__subhead">Ayah</div>
                                    <div class="portal-student-form__field portal-student-form__field--span-4">
                                        <label class="form-label fw-semibold" for="student-father-name">Nama</label>
                                        <input class="form-control" id="student-father-name" name="detail_siswa[father_name]" type="text">
                                    </div>
                                    <div class="portal-student-form__field portal-student-form__field--span-4">
                                        <label class="form-label fw-semibold" for="student-father-education">Pendidikan</label>
                                        <input class="form-control" id="student-father-education" name="detail_siswa[father_education]" type="text">
                                    </div>
                                    <div class="portal-student-form__field portal-student-form__field--span-4">
                                        <label class="form-label fw-semibold" for="student-father-occupation">Pekerjaan</label>
                                        <input class="form-control" id="student-father-occupation" name="detail_siswa[father_occupation]" type="text">
                                    </div>

                                    <div class="portal-student-form__subhead">Ibu</div>
                                    <div class="portal-student-form__field portal-student-form__field--span-4">
                                        <label class="form-label fw-semibold" for="student-mother-name">Nama</label>
                                        <input class="form-control" id="student-mother-name" name="detail_siswa[mother_name]" type="text">
                                    </div>
                                    <div class="portal-student-form__field portal-student-form__field--span-4">
                                        <label class="form-label fw-semibold" for="student-mother-education">Pendidikan</label>
                                        <input class="form-control" id="student-mother-education" name="detail_siswa[mother_education]" type="text">
                                    </div>
                                    <div class="portal-student-form__field portal-student-form__field--span-4">
                                        <label class="form-label fw-semibold" for="student-mother-occupation">Pekerjaan</label>
                                        <input class="form-control" id="student-mother-occupation" name="detail_siswa[mother_occupation]" type="text">
                                    </div>
                                </div>
                            </section>

                            <section class="portal-student-form__section">
                                <div class="portal-student-form__section-head">
                                    <div>
                                        <h3>Kontak Orang Tua / Wali</h3>
                                        <p>Area ini disiapkan untuk alamat penghubung utama orang tua atau wali siswa.</p>
                                    </div>
                                </div>

                                <div class="portal-student-form__grid">
                                    <div class="portal-student-form__field portal-student-form__field--span-12">
                                        <label class="form-label fw-semibold" for="student-parent-address">Alamat</label>
                                        <textarea class="form-control" id="student-parent-address" name="detail_siswa[parent_address]" rows="2"></textarea>
                                    </div>
                                    <div class="portal-student-form__field portal-student-form__field--span-3">
                                        <label class="form-label fw-semibold" for="student-parent-province">Propinsi</label>
                                        <input class="form-control" id="student-parent-province" name="detail_siswa[parent_province]" type="text">
                                    </div>
                                    <div class="portal-student-form__field portal-student-form__field--span-3">
                                        <label class="form-label fw-semibold" for="student-parent-city">Kota</label>
                                        <input class="form-control" id="student-parent-city" name="detail_siswa[parent_city]" type="text">
                                    </div>
                                    <div class="portal-student-form__field portal-student-form__field--span-3">
                                        <label class="form-label fw-semibold" for="student-postal-code">Kode Pos</label>
                                        <input class="form-control" id="student-postal-code" name="detail_siswa[postal_code]" type="text">
                                    </div>
                                    <div class="portal-student-form__field portal-student-form__field--span-3">
                                        <label class="form-label fw-semibold" for="student-parent-phone">Telp</label>
                                        <input class="form-control" id="student-parent-phone" name="detail_siswa[parent_phone]" type="text">
                                    </div>
                                </div>
                            </section>
                        </div>
                    </div>

                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary portal-directory-submit" data-student-submit>Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="student-view-modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-fullscreen-lg-down portal-student-view-modal__dialog">
            <div class="modal-content portal-directory-modal portal-student-view-modal">
                <div class="modal-header border-0 pb-0">
                    <div>
                        <h2 class="modal-title fs-4 fw-bold">Data Siswa</h2>
                        <p class="text-secondary mb-0">Lihat biodata, alamat, dan kontak orang tua dalam satu tampilan.</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>

                <div class="modal-body portal-student-view-modal__body pt-3" data-student-view-content></div>

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
            const directory = document.querySelector('[data-student-directory]');

            if (!directory) {
                return;
            }

            const searchInput = directory.querySelector('[data-directory-search]');
            const filterSelect = directory.querySelector('[data-directory-filter]');
            const feedback = directory.querySelector('[data-directory-feedback]');
            const emptyState = directory.querySelector('[data-directory-empty]');
            const sections = Array.from(directory.querySelectorAll('[data-directory-section]'));
            const rows = Array.from(directory.querySelectorAll('[data-student-row]'));
            const exportAllButton = directory.querySelector('[data-directory-export-all]');
            const sectionExportButtons = Array.from(directory.querySelectorAll('[data-directory-export-section]'));
            const createButtons = Array.from(directory.querySelectorAll('[data-student-create]'));
            const viewButtons = Array.from(directory.querySelectorAll('[data-student-view]'));
            const editButtons = Array.from(directory.querySelectorAll('[data-student-edit]'));
            const deleteButtons = Array.from(directory.querySelectorAll('[data-student-delete]'));
            const studentMap = @json($studentPayload);
            const modalElement = document.getElementById('student-directory-modal');
            const modal = modalElement && window.bootstrap ? window.bootstrap.Modal.getOrCreateInstance(modalElement) : null;
            const viewModalElement = document.getElementById('student-view-modal');
            const viewModal = viewModalElement && window.bootstrap ? window.bootstrap.Modal.getOrCreateInstance(viewModalElement) : null;
            const form = document.querySelector('[data-student-form]');
            const formTitle = document.querySelector('[data-student-form-title]');
            const formErrors = document.querySelector('[data-student-form-errors]');
            const submitButton = document.querySelector('[data-student-submit]');
            const idInput = document.querySelector('[data-student-id]');
            const nameInput = form?.querySelector('[name="name"]') ?? null;
            const photoInput = form?.querySelector('[data-student-photo-input]') ?? null;
            const removePhotoInput = form?.querySelector('[data-student-remove-photo]') ?? null;
            const photoPreviewImage = form?.querySelector('[data-student-photo-preview-image]') ?? null;
            const photoPreviewFallback = form?.querySelector('[data-student-photo-preview-fallback]') ?? null;
            const viewContent = document.querySelector('[data-student-view-content]');
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
                    return 'FS';
                }

                return parts.map(function (part) {
                    return part.charAt(0).toUpperCase();
                }).join('');
            };

            const setPhotoPreview = function (source, name) {
                if (!photoPreviewImage || !photoPreviewFallback) {
                    return;
                }

                if (source) {
                    photoPreviewImage.src = source;
                    photoPreviewImage.classList.remove('d-none');
                    photoPreviewFallback.classList.add('d-none');

                    return;
                }

                photoPreviewImage.removeAttribute('src');
                photoPreviewImage.classList.add('d-none');
                photoPreviewFallback.textContent = buildInitials(nameInput?.value || name);
                photoPreviewFallback.classList.remove('d-none');
            };

            const openViewModal = function (student) {
                if (!student || !viewContent) {
                    return;
                }

                const detailSiswa = student.detail_siswa || {};
                const photoMarkup = student.photo_url
                    ? '<img class="portal-student-view__avatar" src="' + escapeHtml(student.photo_url) + '" alt="' + escapeHtml(student.name) + '">'
                    : '<div class="portal-student-view__avatar portal-student-view__avatar--fallback">' + escapeHtml(buildInitials(student.name)) + '</div>';

                const buildItems = function (items) {
                    return items.map(function (item) {
                        return '<div class="portal-student-view__item"><span>' + escapeHtml(item.label) + '</span><strong>' + escapeHtml(normalizeDisplayValue(item.value)) + '</strong></div>';
                    }).join('');
                };

                viewContent.innerHTML = [
                    '<section class="portal-student-view__hero">',
                        '<div class="portal-student-view__hero-media">' + photoMarkup + '</div>',
                        '<div class="portal-student-view__hero-copy">',
                            '<span class="portal-student-view__eyebrow">Data siswa</span>',
                            '<h3>' + escapeHtml(student.name) + '</h3>',
                            '<div class="portal-student-view__hero-meta">',
                                '<span>' + escapeHtml(normalizeDisplayValue(student.class_name, 'Belum masuk kelas')) + '</span>',
                                '<span>' + escapeHtml(normalizeDisplayValue(student.status, 'Aktif')) + '</span>',
                            '</div>',
                        '</div>',
                    '</section>',
                    '<div class="portal-student-view__grid">',
                        '<section class="portal-student-view__card"><h4>Identitas</h4><div class="portal-student-view__items">' + buildItems([
                            { label: 'NIK', value: student.nik },
                            { label: 'NISN', value: student.nisn },
                            { label: 'Jenis Kelamin', value: student.gender_label },
                            { label: 'Tempat Lahir', value: detailSiswa.birth_place },
                            { label: 'Tanggal Lahir', value: student.birth_date_label },
                            { label: 'Agama', value: detailSiswa.religion },
                            { label: 'No. HP Siswa', value: student.phone },
                            { label: 'Pendidikan Asal', value: detailSiswa.previous_school },
                        ]) + '</div></section>',
                        '<section class="portal-student-view__card"><h4>Alamat Siswa</h4><div class="portal-student-view__items">' + buildItems([
                            { label: 'Alamat Ringkas', value: student.address },
                            { label: 'Jalan', value: detailSiswa.address_street },
                            { label: 'Kelurahan', value: detailSiswa.address_village },
                            { label: 'Kecamatan', value: detailSiswa.address_district },
                            { label: 'Propinsi', value: detailSiswa.address_province },
                            { label: 'Kota', value: detailSiswa.address_city },
                        ]) + '</div></section>',
                        '<section class="portal-student-view__card"><h4>Data Orang Tua</h4><div class="portal-student-view__items">' + buildItems([
                            { label: 'Nama Ayah', value: detailSiswa.father_name },
                            { label: 'Pendidikan Ayah', value: detailSiswa.father_education },
                            { label: 'Pekerjaan Ayah', value: detailSiswa.father_occupation },
                            { label: 'Nama Ibu', value: detailSiswa.mother_name },
                            { label: 'Pendidikan Ibu', value: detailSiswa.mother_education },
                            { label: 'Pekerjaan Ibu', value: detailSiswa.mother_occupation },
                        ]) + '</div></section>',
                        '<section class="portal-student-view__card"><h4>Akses Akun</h4><div class="portal-student-view__items">' + buildItems([
                            { label: 'Status Akun', value: student.has_account ? 'Terhubung ke portal siswa' : 'Belum terhubung' },
                            { label: 'Password Default', value: student.default_password || '' },
                        ]) + '</div></section>',
                        '<section class="portal-student-view__card"><h4>Kontak Orang Tua / Wali</h4><div class="portal-student-view__items">' + buildItems([
                            { label: 'Alamat', value: detailSiswa.parent_address },
                            { label: 'Propinsi', value: detailSiswa.parent_province },
                            { label: 'Kota', value: detailSiswa.parent_city },
                            { label: 'Kode Pos', value: detailSiswa.postal_code },
                            { label: 'Telp', value: detailSiswa.parent_phone },
                            { label: 'Walikelas', value: student.homeroom_teacher },
                            { label: 'No. HP Walikelas', value: student.homeroom_phone },
                        ]) + '</div></section>',
                    '</div>',
                ].join('');

                viewModal?.show();
            };

            const applyFilters = function () {
                const query = (searchInput?.value || '').trim().toLowerCase();
                const classId = filterSelect?.value || '';
                let visibleRows = 0;

                sections.forEach(function (section) {
                    const sectionRows = Array.from(section.querySelectorAll('[data-student-row]'));
                    let sectionHasVisibleRow = false;

                    sectionRows.forEach(function (row) {
                        const matchesQuery = query === '' || (row.dataset.searchText || '').includes(query);
                        const matchesClass = classId === '' || (row.dataset.classId || '') === classId;
                        const isVisible = matchesQuery && matchesClass;

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

                if (query === '' && classId === '') {
                    hideFeedback();

                    return;
                }

                showFeedback('Menampilkan ' + visibleRows + ' data siswa sesuai filter aktif.');
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
                    'NIK',
                    'NISN',
                    'Tanggal Lahir',
                    'Jenis Kelamin',
                    'Kelas',
                    'Walikelas',
                    'No.Hp Wali',
                    'Status',
                ].map(csvEscape).join(',')];

                visibleRows.forEach(function (row) {
                    const cells = Array.from(row.querySelectorAll('td'));

                    lines.push([
                        cells[0]?.innerText || '',
                        cells[1]?.querySelector('.portal-directory-name')?.innerText || '',
                        cells[1]?.querySelector('.portal-directory-meta')?.innerText.replace(/^NIK\s+/, '') || '',
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

            const resetForm = function () {
                if (!form) {
                    return;
                }

                form.reset();
                idInput.value = '';
                formTitle.textContent = 'Tambah Data Siswa';
                submitButton.textContent = 'Simpan';
                submitButton.disabled = false;
                formErrors.classList.add('d-none');
                formErrors.innerHTML = '';
                if (removePhotoInput) {
                    removePhotoInput.checked = false;
                }
                setPhotoPreview('', '');
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

            const fillForm = function (student) {
                if (!form || !student) {
                    return;
                }

                const detailSiswa = student.detail_siswa || {};

                setFormValue('name', student.name);
                setFormValue('nik', student.nik);
                setFormValue('nisn', student.nisn);
                setFormValue('school_class_id', student.school_class_id);
                setFormValue('gender', student.gender);
                setFormValue('birth_date', student.birth_date);
                setFormValue('phone', student.phone);
                setFormValue('address', student.address);
                if (removePhotoInput) {
                    removePhotoInput.checked = false;
                }
                setFormValue('detail_siswa[religion]', detailSiswa.religion);
                setFormValue('detail_siswa[birth_place]', detailSiswa.birth_place);
                setFormValue('detail_siswa[address_street]', detailSiswa.address_street);
                setFormValue('detail_siswa[address_village]', detailSiswa.address_village);
                setFormValue('detail_siswa[address_district]', detailSiswa.address_district);
                setFormValue('detail_siswa[address_province]', detailSiswa.address_province);
                setFormValue('detail_siswa[address_city]', detailSiswa.address_city);
                setFormValue('detail_siswa[father_name]', detailSiswa.father_name);
                setFormValue('detail_siswa[father_education]', detailSiswa.father_education);
                setFormValue('detail_siswa[father_occupation]', detailSiswa.father_occupation);
                setFormValue('detail_siswa[mother_name]', detailSiswa.mother_name);
                setFormValue('detail_siswa[mother_education]', detailSiswa.mother_education);
                setFormValue('detail_siswa[mother_occupation]', detailSiswa.mother_occupation);
                setFormValue('detail_siswa[parent_address]', detailSiswa.parent_address);
                setFormValue('detail_siswa[parent_province]', detailSiswa.parent_province);
                setFormValue('detail_siswa[parent_city]', detailSiswa.parent_city);
                setFormValue('detail_siswa[postal_code]', detailSiswa.postal_code);
                setFormValue('detail_siswa[parent_phone]', detailSiswa.parent_phone);
                setFormValue('detail_siswa[previous_school]', detailSiswa.previous_school);
                setPhotoPreview(student.photo_url, student.name);
            };

            const renderErrors = function (payload) {
                if (!formErrors) {
                    return;
                }

                const messages = payload?.errors
                    ? Object.values(payload.errors).flat()
                    : [payload?.message || 'Terjadi kesalahan saat menyimpan data siswa.'];

                formErrors.innerHTML = messages.map(function (message) {
                    return '<div>' + message + '</div>';
                }).join('');
                formErrors.classList.remove('d-none');
            };

            modalElement?.addEventListener('hidden.bs.modal', function () {
                resetForm();
            });

            nameInput?.addEventListener('input', function () {
                if (photoPreviewImage?.classList.contains('d-none')) {
                    setPhotoPreview('', nameInput.value);
                }
            });

            photoInput?.addEventListener('change', function () {
                const file = photoInput.files && photoInput.files[0] ? photoInput.files[0] : null;

                if (removePhotoInput) {
                    removePhotoInput.checked = false;
                }

                if (!file) {
                    const currentStudent = idInput.value ? studentMap[idInput.value] : null;
                    setPhotoPreview(currentStudent?.photo_url || '', currentStudent?.name || nameInput?.value || '');

                    return;
                }

                const reader = new FileReader();

                reader.onload = function (loadEvent) {
                    setPhotoPreview(loadEvent.target?.result || '', nameInput?.value || file.name);
                };

                reader.readAsDataURL(file);
            });

            removePhotoInput?.addEventListener('change', function () {
                const currentStudent = idInput.value ? studentMap[idInput.value] : null;

                if (removePhotoInput.checked) {
                    if (photoInput) {
                        photoInput.value = '';
                    }

                    setPhotoPreview('', currentStudent?.name || nameInput?.value || '');

                    return;
                }

                setPhotoPreview(currentStudent?.photo_url || '', currentStudent?.name || nameInput?.value || '');
            });

            if (searchInput) {
                searchInput.addEventListener('input', applyFilters);
            }

            if (filterSelect) {
                filterSelect.addEventListener('change', applyFilters);
            }

            exportAllButton?.addEventListener('click', function () {
                window.location.href = '/admin/export/siswa';
            });

            sectionExportButtons.forEach(function (button) {
                button.addEventListener('click', function () {
                    const key = button.dataset.directoryExportSection;
                    const sectionRows = rows.filter(function (row) {
                        return row.dataset.sectionKey === key;
                    });

                    exportRows(sectionRows, 'data-siswa-' + key + '.csv');
                });
            });

            createButtons.forEach(function (button) {
                button.addEventListener('click', function () {
                    resetForm();
                });
            });

            viewButtons.forEach(function (button) {
                button.addEventListener('click', function () {
                    const studentId = button.dataset.studentView;
                    const student = studentMap[studentId];

                    if (!student) {
                        return;
                    }

                    openViewModal(student);
                });
            });

            editButtons.forEach(function (button) {
                button.addEventListener('click', function () {
                    const studentId = button.dataset.studentEdit;
                    const student = studentMap[studentId];

                    if (!student) {
                        return;
                    }

                    resetForm();
                    idInput.value = student.id;
                    formTitle.textContent = 'Ubah Data Siswa';
                    submitButton.textContent = 'Perbarui';
                    fillForm(student);
                    modal?.show();
                });
            });

            deleteButtons.forEach(function (button) {
                button.addEventListener('click', async function () {
                    const studentId = button.dataset.studentDelete;
                    const studentName = button.dataset.studentName || 'siswa';

                    if (!studentId || !window.confirm('Hapus data ' + studentName + '?')) {
                        return;
                    }

                    const formData = new FormData();
                    formData.append('_method', 'DELETE');

                    const response = await fetch('/admin/siswa/' + studentId, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                        },
                        body: formData,
                    });

                    if (!response.ok) {
                        showFeedback('Gagal menghapus data siswa.');

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
                const studentId = idInput.value;
                const endpoint = studentId ? '/admin/siswa/' + studentId : '/admin/siswa';

                if (studentId) {
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
