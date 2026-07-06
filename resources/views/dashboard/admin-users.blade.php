@extends('layouts.portal-dashboard')

@section('title', $pageTitle)

@section('content')
    <div class="portal-dashboard-shell portal-directory-shell" data-crud-page data-endpoint="{{ url('/admin/pengguna') }}" data-resource="pengguna">
        @include('dashboard.partials.sidebar', ['menuSections' => $menuSections])

        <main class="portal-dashboard-main portal-directory-main">
            <div class="portal-directory-stack">
                <section class="portal-panel portal-directory-banner">
                    <div class="portal-directory-banner__bar"></div>
                    <div class="portal-directory-banner__copy">
                        <h1>Manajemen Pengguna</h1>
                        <p>Kelola akun, email, status verifikasi, dan role akses portal.</p>
                    </div>
                    <div class="portal-directory-banner__count">{{ $users->count() }} pengguna</div>
                </section>

                <section class="portal-directory-toolbar">
                    <label class="portal-directory-search">
                        <span class="portal-directory-search__icon">@include('dashboard.partials.icon', ['name' => 'search'])</span>
                        <input type="search" placeholder="Pencarian..." data-search>
                    </label>
                    <label class="portal-directory-filter">
                        <select data-role-filter>
                            <option value="">Semua role</option>
                            @foreach ($roleOptions as $role)
                                <option value="{{ $role }}">{{ $role }}</option>
                            @endforeach
                            <option value="tanpa-role">Tanpa role</option>
                        </select>
                    </label>
                    <label class="portal-directory-filter">
                        <select data-verified-filter>
                            <option value="">Semua status</option>
                            <option value="verified">Terverifikasi</option>
                            <option value="unverified">Belum verifikasi</option>
                        </select>
                    </label>
                    <button class="portal-round-action" type="button" aria-label="Tambah pengguna" data-create data-bs-toggle="modal" data-bs-target="#crud-modal">
                        @include('dashboard.partials.icon', ['name' => 'plus'])
                    </button>
                </section>

                <div class="alert alert-success d-none" data-feedback></div>

                <section class="portal-directory-section">
                    <div class="table-responsive">
                        <table class="table portal-table portal-directory-table mb-0">
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Profil</th>
                                    <th>Verifikasi</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($users as $user)
                                    @php
                                        $userPayload = [
                                            'id' => $user->id,
                                            'name' => $user->name,
                                            'email' => $user->email,
                                            'roles' => $user->roles ?? [],
                                            'email_verified' => $user->email_verified_at !== null,
                                            'teacher_id' => $user->teacherProfile?->id,
                                            'student_id' => $user->studentProfile?->id,
                                        ];
                                    @endphp
                                    <tr data-row data-row-id="{{ $user->id }}" data-role="{{ implode(' ', $user->roles ?? []) ?: 'tanpa-role' }}" data-verified="{{ $user->email_verified_at ? 'verified' : 'unverified' }}" data-search-text="{{ mb_strtolower($user->name.' '.$user->email.' '.implode(' ', $user->roles ?? [])) }}">
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td>{{ implode(', ', $user->roles ?? []) ?: '-' }}</td>
                                        <td>{{ $user->teacherProfile?->name ?? $user->studentProfile?->name ?? '-' }}</td>
                                        <td>{{ $user->email_verified_at ? 'Terverifikasi' : 'Belum' }}</td>
                                        <td>
                                            <div class="portal-directory-actions">
                                                <button class="portal-directory-action is-edit" type="button" data-edit='@json($userPayload)'>@include('dashboard.partials.icon', ['name' => 'edit'])</button>
                                                <button class="portal-directory-action is-delete" type="button" data-delete="{{ $user->id }}" data-name="{{ $user->name }}">@include('dashboard.partials.icon', ['name' => 'trash'])</button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="portal-directory-feedback d-none" data-empty-state>Tidak ada pengguna yang cocok.</div>
                    <div class="d-flex justify-content-between align-items-center gap-3 p-3" data-pagination>
                        <button class="btn btn-light btn-sm" type="button" data-prev>Prev</button>
                        <span class="small text-muted" data-page-info></span>
                        <button class="btn btn-light btn-sm" type="button" data-next>Next</button>
                    </div>
                </section>
            </div>
        </main>
    </div>

    <div class="modal fade" id="crud-modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content portal-directory-modal">
                <form data-crud-form>
                    <div class="modal-header border-0 pb-0">
                        <h2 class="modal-title fs-4 fw-bold" data-form-title>Tambah Pengguna</h2>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-danger d-none" data-errors></div>
                        <input type="hidden" name="id">
                        <div class="mb-3">
                            <label class="form-label">Nama</label>
                            <input class="form-control" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input class="form-control" name="email" type="email" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input class="form-control" name="password" type="password" minlength="8" placeholder="Isi saat membuat / mengganti password">
                        </div>
                        <div class="mb-3">
                            <label class="form-label d-block">Role</label>
                            @foreach ($roleOptions as $role)
                                <label class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="roles[]" value="{{ $role }}">
                                    <span class="form-check-label">{{ $role }}</span>
                                </label>
                            @endforeach
                        </div>
                        <label class="form-check">
                            <input class="form-check-input" type="checkbox" name="email_verified" value="1" checked>
                            <span class="form-check-label">Email terverifikasi</span>
                        </label>
                        <div class="row g-3 mt-1">
                            <div class="col-md-6">
                                <label class="form-label">Hubungkan Profil Guru</label>
                                <select class="form-select" name="teacher_id">
                                    <option value="">Tidak terhubung</option>
                                    @foreach ($teacherOptions as $teacher)
                                        <option value="{{ $teacher->id }}">{{ $teacher->name }}{{ $teacher->user ? ' - '.$teacher->user->email : '' }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Hubungkan Profil Siswa</label>
                                <select class="form-select" name="student_id">
                                    <option value="">Tidak terhubung</option>
                                    @foreach ($studentOptions as $student)
                                        <option value="{{ $student->id }}">{{ $student->name }}{{ $student->user ? ' - '.$student->user->email : '' }}</option>
                                    @endforeach
                                </select>
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
            const page = document.querySelector('[data-crud-page]');
            const form = document.querySelector('[data-crud-form]');
            const modal = window.bootstrap.Modal.getOrCreateInstance(document.getElementById('crud-modal'));
            const errors = document.querySelector('[data-errors]');
            const feedback = document.querySelector('[data-feedback]');
            const search = document.querySelector('[data-search]');
            const roleFilter = document.querySelector('[data-role-filter]');
            const verifiedFilter = document.querySelector('[data-verified-filter]');
            const emptyState = document.querySelector('[data-empty-state]');
            const pageInfo = document.querySelector('[data-page-info]');
            const prevButton = document.querySelector('[data-prev]');
            const nextButton = document.querySelector('[data-next]');
            const token = '{{ csrf_token() }}';
            const pageSize = 10;
            let currentPage = 1;
            const editIcon = document.querySelector('[data-edit]')?.innerHTML || 'Edit';
            const deleteIcon = document.querySelector('[data-delete]')?.innerHTML || 'Hapus';

            const escapeHtml = function (value) {
                return String(value ?? '')
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;')
                    .replace(/'/g, '&#039;');
            };

            const profileLabel = function (user) {
                return user.teacher_profile?.name || user.student_profile?.name || '-';
            };

            const payloadFor = function (user) {
                return {
                    id: user.id,
                    name: user.name,
                    email: user.email,
                    roles: user.roles || [],
                    email_verified: Boolean(user.email_verified_at),
                    teacher_id: user.teacher_profile?.id || null,
                    student_id: user.student_profile?.id || null,
                };
            };

            const rowHtml = function (user) {
                const roles = user.roles || [];
                const roleLabel = roles.length ? roles.join(', ') : '-';
                const roleData = roles.length ? roles.join(' ') : 'tanpa-role';
                const verified = user.email_verified_at ? 'verified' : 'unverified';
                const payload = escapeHtml(JSON.stringify(payloadFor(user)));
                const searchText = escapeHtml((user.name + ' ' + user.email + ' ' + roles.join(' ')).toLowerCase());

                return '' +
                    '<tr data-row data-row-id="' + escapeHtml(user.id) + '" data-role="' + escapeHtml(roleData) + '" data-verified="' + verified + '" data-search-text="' + searchText + '">' +
                        '<td>' + escapeHtml(user.name) + '</td>' +
                        '<td>' + escapeHtml(user.email) + '</td>' +
                        '<td>' + escapeHtml(roleLabel) + '</td>' +
                        '<td>' + escapeHtml(profileLabel(user)) + '</td>' +
                        '<td>' + (user.email_verified_at ? 'Terverifikasi' : 'Belum') + '</td>' +
                        '<td><div class="portal-directory-actions">' +
                            '<button class="portal-directory-action is-edit" type="button" data-edit=\'' + payload + '\'>' + editIcon + '</button>' +
                            '<button class="portal-directory-action is-delete" type="button" data-delete="' + escapeHtml(user.id) + '" data-name="' + escapeHtml(user.name) + '">' + deleteIcon + '</button>' +
                        '</div></td>' +
                    '</tr>';
            };

            const bindRowActions = function (root) {
                root.querySelectorAll('[data-edit]').forEach(function (button) {
                    if (button.dataset.bound === '1') return;
                    button.dataset.bound = '1';
                    button.addEventListener('click', function () {
                        resetForm();
                        const data = JSON.parse(button.dataset.edit);
                        document.querySelector('[data-form-title]').textContent = 'Ubah Pengguna';
                        form.elements.id.value = data.id;
                        form.elements.name.value = data.name || '';
                        form.elements.email.value = data.email || '';
                        form.querySelectorAll('[name="roles[]"]').forEach((input) => input.checked = (data.roles || []).includes(input.value));
                        form.elements.email_verified.checked = Boolean(data.email_verified);
                        form.elements.teacher_id.value = data.teacher_id || '';
                        form.elements.student_id.value = data.student_id || '';
                        modal.show();
                    });
                });

                root.querySelectorAll('[data-delete]').forEach(function (button) {
                    if (button.dataset.bound === '1') return;
                    button.dataset.bound = '1';
                    button.addEventListener('click', async function () {
                        if (!confirm('Hapus pengguna ' + button.dataset.name + '?')) return;
                        const response = await fetch(page.dataset.endpoint + '/' + button.dataset.delete, {
                            method: 'POST',
                            headers: {'X-CSRF-TOKEN': token, 'Accept': 'application/json'},
                            body: new URLSearchParams({'_method': 'DELETE'}),
                        });
                        if (response.ok) {
                            button.closest('[data-row]')?.remove();
                            showFeedback('Pengguna berhasil dihapus.');
                            applyFilters();
                        } else {
                            showErrors(await response.json().catch(() => ({})));
                        }
                    });
                });
            };

            const showErrors = function (payload) {
                const messages = payload?.errors ? Object.values(payload.errors).flat() : [payload?.message || 'Proses gagal.'];
                errors.innerHTML = messages.map((message) => '<div>' + message + '</div>').join('');
                errors.classList.remove('d-none');
            };

            const resetForm = function () {
                form.reset();
                form.elements.id.value = '';
                errors.classList.add('d-none');
                feedback.classList.add('d-none');
                document.querySelector('[data-form-title]').textContent = 'Tambah Pengguna';
            };

            const showFeedback = function (message) {
                feedback.textContent = message;
                feedback.classList.remove('d-none');
            };

            document.querySelector('[data-create]').addEventListener('click', resetForm);

            bindRowActions(document);

            form.addEventListener('submit', async function (event) {
                event.preventDefault();
                errors.classList.add('d-none');
                const data = new FormData(form);
                const id = data.get('id');
                if (id) data.append('_method', 'PUT');
                if (!data.get('email_verified')) data.set('email_verified', '0');
                const response = await fetch(page.dataset.endpoint + (id ? '/' + id : ''), {
                    method: 'POST',
                    headers: {'X-CSRF-TOKEN': token, 'Accept': 'application/json'},
                    body: data,
                });
                const payload = await response.json().catch(() => ({}));
                if (response.ok) {
                    const html = rowHtml(payload.data);
                    const existing = document.querySelector('[data-row-id="' + payload.data.id + '"]');
                    if (existing) {
                        existing.outerHTML = html;
                        bindRowActions(document.querySelector('[data-row-id="' + payload.data.id + '"]'));
                    } else {
                        document.querySelector('tbody').insertAdjacentHTML('afterbegin', html);
                        bindRowActions(document.querySelector('[data-row-id="' + payload.data.id + '"]'));
                    }
                    modal.hide();
                    showFeedback(payload.message || 'Pengguna berhasil disimpan.');
                    applyFilters();
                } else {
                    showErrors(payload);
                }
            });

            const applyFilters = function () {
                const keyword = search.value.trim().toLowerCase();
                const role = roleFilter.value;
                const verified = verifiedFilter.value;
                const rows = Array.from(document.querySelectorAll('[data-row]'));
                const matched = rows.filter(function (row) {
                    return row.dataset.searchText.includes(keyword)
                        && (!role || row.dataset.role.split(' ').includes(role))
                        && (!verified || row.dataset.verified === verified);
                });
                const totalPages = Math.max(Math.ceil(matched.length / pageSize), 1);
                currentPage = Math.min(currentPage, totalPages);
                rows.forEach((row) => row.classList.add('d-none'));
                matched.slice((currentPage - 1) * pageSize, currentPage * pageSize).forEach((row) => row.classList.remove('d-none'));
                emptyState.classList.toggle('d-none', matched.length > 0);
                pageInfo.textContent = matched.length + ' data | halaman ' + currentPage + ' dari ' + totalPages;
                prevButton.disabled = currentPage <= 1;
                nextButton.disabled = currentPage >= totalPages;
            };

            [search, roleFilter, verifiedFilter].forEach((input) => input.addEventListener('input', function () {
                currentPage = 1;
                applyFilters();
            }));
            prevButton.addEventListener('click', function () { currentPage -= 1; applyFilters(); });
            nextButton.addEventListener('click', function () { currentPage += 1; applyFilters(); });

            applyFilters();
        });
    </script>
@endpush
