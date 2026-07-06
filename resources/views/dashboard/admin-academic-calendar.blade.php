@extends('layouts.portal-dashboard')

@section('title', $pageTitle)

@section('content')
    <div class="portal-dashboard-shell portal-directory-shell" data-academic-calendar-page data-endpoint="{{ url('/admin/kalender-akademik-data') }}">
        @include('dashboard.partials.sidebar', ['menuSections' => $menuSections])

        <main class="portal-dashboard-main portal-directory-main">
            <div class="portal-directory-stack">
                <section class="portal-panel portal-directory-banner">
                    <div class="portal-directory-banner__bar"></div>
                    <div class="portal-directory-banner__copy">
                        <h1>Kalender Akademik</h1>
                        <p>Agenda sekolah berdasarkan tahun ajaran {{ $activeAcademicYear }} semester {{ ucfirst($activeSemester) }}.</p>
                    </div>
                    <div class="portal-directory-banner__count">{{ $semesterLock ? 'Semester ditutup' : $events->count().' agenda' }}</div>
                </section>

                <section class="portal-panel portal-semester-lock-card" data-semester-lock-card>
                    <div>
                        <strong>Status Semester</strong>
                        <span data-lock-status>
                            @if ($semesterLock)
                                Ditutup pada {{ $semesterLock->locked_at?->format('d-m-Y H:i') }} oleh {{ $semesterLock->lockedBy?->name ?? 'Admin' }}.
                            @else
                                Semester masih terbuka. Guru dan wali kelas dapat menyimpan absensi pada hari efektif.
                            @endif
                        </span>
                    </div>
                    <form data-lock-form>
                        <input type="hidden" name="academic_year" value="{{ $activeAcademicYear }}">
                        <input type="hidden" name="semester" value="{{ $activeSemester }}">
                        <input class="form-control" name="notes" placeholder="Catatan penutupan semester">
                        <button class="btn {{ $semesterLock ? 'btn-outline-primary' : 'btn-primary' }}" type="submit" data-lock-submit data-locked="{{ $semesterLock ? '1' : '0' }}">
                            {{ $semesterLock ? 'Buka Lock' : 'Tutup Semester' }}
                        </button>
                    </form>
                </section>

                <section class="portal-directory-toolbar">
                    <label class="portal-directory-search">
                        <span class="portal-directory-search__icon">@include('dashboard.partials.icon', ['name' => 'search'])</span>
                        <input type="search" placeholder="Pencarian..." data-search>
                    </label>
                    <label class="portal-directory-filter">
                        <select data-category-filter>
                            <option value="">Semua kategori</option>
                            @foreach ($events->pluck('category')->filter()->unique()->sort()->values() as $category)
                                <option value="{{ $category }}">{{ $category }}</option>
                            @endforeach
                        </select>
                    </label>
                    <button class="portal-round-action" type="button" data-create data-bs-toggle="modal" data-bs-target="#academic-calendar-modal">@include('dashboard.partials.icon', ['name' => 'plus'])</button>
                </section>

                <div class="alert alert-success d-none" data-feedback></div>

                <section class="portal-directory-section">
                    <div class="table-responsive">
                        <table class="table portal-table portal-directory-table mb-0">
                            <thead>
                                <tr>
                                    <th>Agenda</th>
                                    <th>Kategori</th>
                                    <th>Jenis</th>
                                    <th>Periode</th>
                                    <th>Tahun Ajaran</th>
                                    <th>Semester</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($events as $event)
                                    <tr data-row data-row-id="{{ $event->id }}" data-category="{{ $event->category }}" data-search-text="{{ mb_strtolower($event->title.' '.$event->category.' '.$event->description) }}">
                                        <td>
                                            <div class="portal-directory-name">{{ $event->title }}</div>
                                            <div class="portal-directory-meta">{{ $event->description ?: '-' }}</div>
                                        </td>
                                        <td>{{ $event->category }}</td>
                                        <td>{{ $eventTypes[$event->type] ?? $event->type }}</td>
                                        <td>{{ $event->start_date?->format('d-m-Y') }} s/d {{ $event->end_date?->format('d-m-Y') }}</td>
                                        <td>{{ $event->academic_year }}</td>
                                        <td>{{ ucfirst($event->semester) }}</td>
                                        <td>
                                            <span class="portal-directory-status {{ $event->is_holiday ? 'is-warning' : '' }}">{{ $event->is_holiday ? 'Libur' : 'Agenda' }}</span>
                                            <span class="portal-directory-status {{ $event->is_active ? '' : 'is-warning' }}">{{ $event->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                                        </td>
                                        <td>
                                            <div class="portal-directory-actions">
                                                <button class="portal-directory-action is-edit" type="button" data-edit='@json($event)'>@include('dashboard.partials.icon', ['name' => 'edit'])</button>
                                                <button class="portal-directory-action is-delete" type="button" data-delete="{{ $event->id }}" data-name="{{ $event->title }}">@include('dashboard.partials.icon', ['name' => 'trash'])</button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="portal-directory-feedback d-none" data-empty-state>Tidak ada agenda yang cocok.</div>
                </section>
            </div>
        </main>
    </div>

    <div class="modal fade" id="academic-calendar-modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content portal-directory-modal">
                <form data-calendar-form>
                    <div class="modal-header border-0 pb-0">
                        <h2 class="modal-title fs-4 fw-bold" data-form-title>Tambah Agenda</h2>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-danger d-none" data-errors></div>
                        <input type="hidden" name="id">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Tahun Ajaran</label>
                                <input class="form-control" name="academic_year" value="{{ $activeAcademicYear }}" placeholder="2025/2026" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Semester</label>
                                <select class="form-select" name="semester" required>
                                    <option value="ganjil" @selected($activeSemester === 'ganjil')>Ganjil</option>
                                    <option value="genap" @selected($activeSemester === 'genap')>Genap</option>
                                </select>
                            </div>
                            <div class="col-md-7">
                                <label class="form-label">Judul</label>
                                <input class="form-control" name="title" required>
                            </div>
                            <div class="col-md-5">
                                <label class="form-label">Kategori</label>
                                <input class="form-control" name="category" placeholder="Ujian, Libur, Kegiatan" required>
                            </div>
                            <div class="col-md-5">
                                <label class="form-label">Jenis Agenda</label>
                                <select class="form-select" name="type" required>
                                    @foreach ($eventTypes as $typeKey => $typeLabel)
                                        <option value="{{ $typeKey }}">{{ $typeLabel }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Tanggal Mulai</label>
                                <input class="form-control" type="date" name="start_date" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Tanggal Selesai</label>
                                <input class="form-control" type="date" name="end_date" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Deskripsi</label>
                                <textarea class="form-control" name="description" rows="3"></textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-check">
                                    <input class="form-check-input" type="checkbox" name="is_holiday" value="1">
                                    <span class="form-check-label">Tandai sebagai hari libur</span>
                                </label>
                            </div>
                            <div class="col-md-6">
                                <label class="form-check">
                                    <input class="form-check-input" type="checkbox" name="is_active" value="1" checked>
                                    <span class="form-check-label">Agenda aktif</span>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0">
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
            const page = document.querySelector('[data-academic-calendar-page]');
            const form = document.querySelector('[data-calendar-form]');
            const lockForm = document.querySelector('[data-lock-form]');
            const lockSubmit = document.querySelector('[data-lock-submit]');
            const lockStatus = document.querySelector('[data-lock-status]');
            const modal = window.bootstrap.Modal.getOrCreateInstance(document.getElementById('academic-calendar-modal'));
            const errors = document.querySelector('[data-errors]');
            const feedback = document.querySelector('[data-feedback]');
            const search = document.querySelector('[data-search]');
            const categoryFilter = document.querySelector('[data-category-filter]');
            const emptyState = document.querySelector('[data-empty-state]');
            const token = '{{ csrf_token() }}';
            const editIcon = document.querySelector('[data-edit]')?.innerHTML || 'Edit';
            const deleteIcon = document.querySelector('[data-delete]')?.innerHTML || 'Hapus';
            const eventTypes = @json($eventTypes);

            const escapeHtml = function (value) {
                return String(value ?? '')
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;')
                    .replace(/'/g, '&#039;');
            };
            const dateOnly = function (value) {
                return value ? String(value).slice(0, 10) : '';
            };
            const formatDate = function (value) {
                const date = dateOnly(value);

                if (!date) {
                    return '-';
                }

                const parts = date.split('-');

                return parts[2] + '-' + parts[1] + '-' + parts[0];
            };
            const rowHtml = function (event) {
                const payload = escapeHtml(JSON.stringify(event));
                const searchText = escapeHtml(((event.title || '') + ' ' + (event.category || '') + ' ' + (event.description || '')).toLowerCase());

                return '' +
                    '<tr data-row data-row-id="' + escapeHtml(event.id) + '" data-category="' + escapeHtml(event.category) + '" data-search-text="' + searchText + '">' +
                        '<td><div class="portal-directory-name">' + escapeHtml(event.title) + '</div><div class="portal-directory-meta">' + escapeHtml(event.description || '-') + '</div></td>' +
                        '<td>' + escapeHtml(event.category) + '</td>' +
                        '<td>' + escapeHtml(eventTypes[event.type] || event.type || '-') + '</td>' +
                        '<td>' + formatDate(event.start_date) + ' s/d ' + formatDate(event.end_date) + '</td>' +
                        '<td>' + escapeHtml(event.academic_year) + '</td>' +
                        '<td>' + escapeHtml((event.semester || '').charAt(0).toUpperCase() + (event.semester || '').slice(1)) + '</td>' +
                        '<td><span class="portal-directory-status ' + (event.is_holiday ? 'is-warning' : '') + '">' + (event.is_holiday ? 'Libur' : 'Agenda') + '</span> ' +
                        '<span class="portal-directory-status ' + (event.is_active ? '' : 'is-warning') + '">' + (event.is_active ? 'Aktif' : 'Nonaktif') + '</span></td>' +
                        '<td><div class="portal-directory-actions">' +
                            '<button class="portal-directory-action is-edit" type="button" data-edit=\'' + payload + '\'>' + editIcon + '</button>' +
                            '<button class="portal-directory-action is-delete" type="button" data-delete="' + escapeHtml(event.id) + '" data-name="' + escapeHtml(event.title) + '">' + deleteIcon + '</button>' +
                        '</div></td>' +
                    '</tr>';
            };
            const resetForm = function () {
                form.reset();
                form.elements.id.value = '';
                form.elements.academic_year.value = '{{ $activeAcademicYear }}';
                form.elements.semester.value = '{{ $activeSemester }}';
                form.elements.is_active.checked = true;
                form.elements.is_holiday.checked = false;
                errors.classList.add('d-none');
                document.querySelector('[data-form-title]').textContent = 'Tambah Agenda';
            };
            const showErrors = function (payload) {
                const messages = payload?.errors ? Object.values(payload.errors).flat() : [payload?.message || 'Proses gagal.'];
                errors.innerHTML = messages.map(function (message) { return '<div>' + escapeHtml(message) + '</div>'; }).join('');
                errors.classList.remove('d-none');
            };
            const applyFilters = function () {
                const keyword = search.value.trim().toLowerCase();
                const category = categoryFilter.value;
                let visible = 0;

                document.querySelectorAll('[data-row]').forEach(function (row) {
                    const matches = row.dataset.searchText.includes(keyword) && (!category || row.dataset.category === category);
                    row.classList.toggle('d-none', !matches);
                    if (matches) visible += 1;
                });

                emptyState.classList.toggle('d-none', visible > 0);
            };
            const bindActions = function (root) {
                root.querySelectorAll('[data-edit]').forEach(function (button) {
                    if (button.dataset.bound === '1') return;
                    button.dataset.bound = '1';
                    button.addEventListener('click', function () {
                        resetForm();
                        const event = JSON.parse(button.dataset.edit);
                        document.querySelector('[data-form-title]').textContent = 'Ubah Agenda';
                        ['id', 'academic_year', 'semester', 'title', 'category', 'type', 'description'].forEach(function (field) {
                            form.elements[field].value = event[field] || '';
                        });
                        form.elements.start_date.value = dateOnly(event.start_date);
                        form.elements.end_date.value = dateOnly(event.end_date);
                        form.elements.is_holiday.checked = Boolean(event.is_holiday);
                        form.elements.is_active.checked = Boolean(event.is_active);
                        modal.show();
                    });
                });
                root.querySelectorAll('[data-delete]').forEach(function (button) {
                    if (button.dataset.bound === '1') return;
                    button.dataset.bound = '1';
                    button.addEventListener('click', async function () {
                        if (!confirm('Hapus agenda ' + button.dataset.name + '?')) return;
                        const response = await fetch(page.dataset.endpoint + '/' + button.dataset.delete, {
                            method: 'POST',
                            headers: {'X-CSRF-TOKEN': token, 'Accept': 'application/json'},
                            body: new URLSearchParams({'_method': 'DELETE'}),
                        });
                        if (response.ok) {
                            button.closest('[data-row]')?.remove();
                            feedback.textContent = 'Agenda berhasil dihapus.';
                            feedback.classList.remove('d-none');
                            applyFilters();
                        }
                    });
                });
            };

            document.querySelector('[data-create]').addEventListener('click', resetForm);
            search.addEventListener('input', applyFilters);
            categoryFilter.addEventListener('change', applyFilters);
            bindActions(document);

            form.addEventListener('submit', async function (event) {
                event.preventDefault();
                const data = new FormData(form);
                const id = data.get('id');
                data.set('is_holiday', form.elements.is_holiday.checked ? '1' : '0');
                data.set('is_active', form.elements.is_active.checked ? '1' : '0');
                if (id) data.append('_method', 'PUT');

                const response = await fetch(page.dataset.endpoint + (id ? '/' + id : ''), {
                    method: 'POST',
                    headers: {'X-CSRF-TOKEN': token, 'Accept': 'application/json'},
                    body: data,
                });
                const payload = await response.json().catch(function () { return {}; });

                if (!response.ok) {
                    showErrors(payload);
                    return;
                }

                const html = rowHtml(payload.data);
                const existing = document.querySelector('[data-row-id="' + payload.data.id + '"]');
                if (existing) {
                    existing.outerHTML = html;
                } else {
                    document.querySelector('tbody').insertAdjacentHTML('afterbegin', html);
                }
                bindActions(document.querySelector('[data-row-id="' + payload.data.id + '"]'));
                modal.hide();
                feedback.textContent = payload.message || 'Agenda berhasil disimpan.';
                feedback.classList.remove('d-none');
                applyFilters();
            });

            lockForm?.addEventListener('submit', async function (event) {
                event.preventDefault();
                const locked = lockSubmit.dataset.locked === '1';
                const data = new FormData(lockForm);
                const response = await fetch('/admin/semester-lock', {
                    method: locked ? 'POST' : 'POST',
                    headers: {'X-CSRF-TOKEN': token, 'Accept': 'application/json'},
                    body: locked
                        ? new URLSearchParams({
                            '_method': 'DELETE',
                            'academic_year': data.get('academic_year'),
                            'semester': data.get('semester'),
                        })
                        : data,
                });
                const payload = await response.json().catch(function () { return {}; });

                if (!response.ok) {
                    alert(payload?.message || 'Gagal mengubah status semester.');
                    return;
                }

                if (locked) {
                    lockSubmit.dataset.locked = '0';
                    lockSubmit.textContent = 'Tutup Semester';
                    lockSubmit.classList.remove('btn-outline-primary');
                    lockSubmit.classList.add('btn-primary');
                    lockStatus.textContent = 'Semester masih terbuka. Guru dan wali kelas dapat menyimpan absensi pada hari efektif.';
                } else {
                    lockSubmit.dataset.locked = '1';
                    lockSubmit.textContent = 'Buka Lock';
                    lockSubmit.classList.remove('btn-primary');
                    lockSubmit.classList.add('btn-outline-primary');
                    lockStatus.textContent = 'Semester sudah ditutup. Absensi semester ini sekarang readonly.';
                }

                feedback.textContent = payload.message || 'Status semester berhasil diperbarui.';
                feedback.classList.remove('d-none');
            });
        });
    </script>
@endpush
