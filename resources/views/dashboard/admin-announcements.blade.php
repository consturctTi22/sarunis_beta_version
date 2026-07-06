@extends('layouts.portal-dashboard')

@section('title', $pageTitle)

@section('content')
    <div class="portal-dashboard-shell portal-directory-shell" data-announcements-page data-endpoint="{{ url('/admin/announcements') }}">
        @include('dashboard.partials.sidebar', ['menuSections' => $menuSections])

        <main class="portal-dashboard-main portal-directory-main">
            <div class="portal-directory-stack">
                <section class="portal-panel portal-directory-banner">
                    <div class="portal-directory-banner__bar"></div>
                    <div class="portal-directory-banner__copy">
                        <h1>Pengumuman</h1>
                        <p>Kelola dan bagikan pengumuman sekolah kepada role tertentu atau semua pengguna.</p>
                    </div>
                    <div class="portal-directory-banner__count">{{ $announcements->count() }} pengumuman</div>
                </section>

                <section class="portal-directory-toolbar">
                    <label class="portal-directory-search">
                        <span class="portal-directory-search__icon">@include('dashboard.partials.icon', ['name' => 'search'])</span>
                        <input type="search" placeholder="Cari pengumuman..." data-search>
                    </label>
                    <button class="portal-round-action" type="button" data-create data-bs-toggle="modal" data-bs-target="#announcement-modal">@include('dashboard.partials.icon', ['name' => 'plus'])</button>
                </section>

                <div class="alert alert-success d-none" data-feedback></div>

                <section class="portal-directory-section">
                    <div class="table-responsive">
                        <table class="table portal-table portal-directory-table mb-0">
                            <thead>
                                <tr>
                                    <th>Judul</th>
                                    <th>Isi Pengumuman</th>
                                    <th>Target Penerima</th>
                                    <th>Pembuat</th>
                                    <th>Tanggal Dibuat</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($announcements as $announcement)
                                    <tr data-row data-row-id="{{ $announcement->id }}" data-search-text="{{ mb_strtolower($announcement->title.' '.$announcement->content.' '.$announcement->target_roles_label) }}">
                                        <td><strong>{{ $announcement->title }}</strong></td>
                                        <td>{{ Str::limit(strip_tags($announcement->content), 80) }}</td>
                                        <td>
                                            @if ($announcement->target_roles)
                                                @foreach ($announcement->target_roles as $role)
                                                    <span class="badge bg-secondary text-capitalize">{{ str_replace('_', ' ', $role) }}</span>
                                                @endforeach
                                            @else
                                                <span class="badge bg-success">Semua Role</span>
                                            @endif
                                        </td>
                                        <td>{{ $announcement->creator?->name ?? 'System' }}</td>
                                        <td>{{ $announcement->created_at->format('d-m-Y H:i') }}</td>
                                        <td>
                                            <div class="portal-directory-actions">
                                                <button class="portal-directory-action is-edit" type="button" data-edit='@json($announcementPayload[$announcement->id])'>@include('dashboard.partials.icon', ['name' => 'edit'])</button>
                                                <button class="portal-directory-action is-delete" type="button" data-delete="{{ $announcement->id }}" data-name="{{ $announcement->title }}">@include('dashboard.partials.icon', ['name' => 'trash'])</button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="portal-directory-feedback d-none" data-empty-state>Tidak ada pengumuman yang cocok.</div>
                    <div class="d-flex justify-content-between align-items-center gap-3 p-3">
                        <button class="btn btn-light btn-sm" type="button" data-prev>Sebelumnya</button>
                        <span class="small text-muted" data-page-info></span>
                        <button class="btn btn-light btn-sm" type="button" data-next>Selanjutnya</button>
                    </div>
                </section>
            </div>
        </main>
    </div>

    <!-- Modal Form -->
    <div class="modal fade" id="announcement-modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content portal-directory-modal">
                <form data-announcement-form>
                    <div class="modal-header border-0 pb-0">
                        <h2 class="modal-title fs-4 fw-bold" data-form-title>Buat Pengumuman Baru</h2>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-danger d-none" data-errors></div>
                        <input type="hidden" name="id">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label">Judul Pengumuman</label>
                                <input class="form-control" name="title" required placeholder="Tuliskan judul pengumuman">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Isi Pengumuman</label>
                                <textarea class="form-control" name="content" rows="6" required placeholder="Tuliskan isi pengumuman secara detail..."></textarea>
                            </div>
                            <div class="col-12">
                                <label class="form-label d-block mb-2">Target Penerima (Role)</label>
                                <div class="row g-2 px-1">
                                    @foreach ([
                                        'admin' => 'Admin',
                                        'guru_mapel' => 'Guru Mapel',
                                        'siswa' => 'Siswa',
                                        'wakasek_kesiswaan' => 'Wakasek Kesiswaan',
                                        'guru_piket' => 'Guru Piket',
                                        'orang_tua' => 'Orang Tua'
                                    ] as $val => $label)
                                        <div class="col-md-4 col-sm-6">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="target_roles[]" value="{{ $val }}" id="role_{{ $val }}">
                                                <label class="form-check-label" for="role_{{ $val }}">
                                                    {{ $label }}
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="form-text text-muted mt-2">
                                    * Kosongkan pilihan jika ingin membagikan pengumuman ke <strong>Semua Role</strong>.
                                </div>
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
            const page = document.querySelector('[data-announcements-page]');
            const form = document.querySelector('[data-announcement-form]');
            const modal = window.bootstrap.Modal.getOrCreateInstance(document.getElementById('announcement-modal'));
            const errors = document.querySelector('[data-errors]');
            const feedback = document.querySelector('[data-feedback]');
            const search = document.querySelector('[data-search]');
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

            const rowHtml = function (announcement) {
                const payload = escapeHtml(JSON.stringify({
                    id: announcement.id,
                    title: announcement.title,
                    content: announcement.content,
                    target_roles: announcement.target_roles || []
                }));

                let targetRolesBadge = '';
                if (announcement.target_roles && announcement.target_roles.length > 0) {
                    announcement.target_roles.forEach(role => {
                        const cleanRole = role.replace('_', ' ');
                        targetRolesBadge += '<span class="badge bg-secondary text-capitalize me-1">' + escapeHtml(cleanRole) + '</span>';
                    });
                } else {
                    targetRolesBadge = '<span class="badge bg-success">Semua Role</span>';
                }

                const creatorName = announcement.creator ? announcement.creator.name : 'System';
                const createdDate = announcement.created_at ? new Date(announcement.created_at) : new Date();
                const formattedDate = String(createdDate.getDate()).padStart(2, '0') + '-' + 
                                      String(createdDate.getMonth() + 1).padStart(2, '0') + '-' + 
                                      createdDate.getFullYear() + ' ' + 
                                      String(createdDate.getHours()).padStart(2, '0') + ':' + 
                                      String(createdDate.getMinutes()).padStart(2, '0');

                const contentLimit = announcement.content.replace(/<[^>]*>/g, '');
                const contentTruncated = contentLimit.length > 80 ? contentLimit.substring(0, 80) + '...' : contentLimit;

                const searchText = escapeHtml((announcement.title + ' ' + announcement.content + ' ' + (announcement.target_roles ? announcement.target_roles.join(' ') : 'semua role')).toLowerCase());

                return '' +
                    '<tr data-row data-row-id="' + escapeHtml(announcement.id) + '" data-search-text="' + searchText + '">' +
                        '<td><strong>' + escapeHtml(announcement.title) + '</strong></td>' +
                        '<td>' + escapeHtml(contentTruncated) + '</td>' +
                        '<td>' + targetRolesBadge + '</td>' +
                        '<td>' + escapeHtml(creatorName) + '</td>' +
                        '<td>' + escapeHtml(formattedDate) + '</td>' +
                        '<td><div class="portal-directory-actions">' +
                            '<button class="portal-directory-action is-edit" type="button" data-edit=\'' + payload + '\'>' + editIcon + '</button>' +
                            '<button class="portal-directory-action is-delete" type="button" data-delete="' + escapeHtml(announcement.id) + '" data-name="' + escapeHtml(announcement.title) + '">' + deleteIcon + '</button>' +
                        '</div></td>' +
                    '</tr>';
            };

            const bindRowActions = function (root) {
                root.querySelectorAll('[data-edit]').forEach((button) => {
                    if (button.dataset.bound === '1') return;
                    button.dataset.bound = '1';
                    button.addEventListener('click', () => {
                        resetForm();
                        const data = JSON.parse(button.dataset.edit);
                        document.querySelector('[data-form-title]').textContent = 'Ubah Pengumuman';
                        form.elements.id.value = data.id || '';
                        form.elements.title.value = data.title || '';
                        form.elements.content.value = data.content || '';
                        
                        // Check checkbox values
                        const targetRoles = data.target_roles || [];
                        form.querySelectorAll('[name="target_roles[]"]').forEach(cb => {
                            cb.checked = targetRoles.includes(cb.value);
                        });

                        modal.show();
                    });
                });

                root.querySelectorAll('[data-delete]').forEach((button) => {
                    if (button.dataset.bound === '1') return;
                    button.dataset.bound = '1';
                    button.addEventListener('click', async () => {
                        if (!confirm('Hapus pengumuman "' + button.dataset.name + '"?')) return;
                        const response = await fetch(page.dataset.endpoint + '/' + button.dataset.delete, {
                            method: 'POST',
                            headers: {'X-CSRF-TOKEN': token, 'Accept': 'application/json'},
                            body: new URLSearchParams({'_method': 'DELETE'}),
                        });
                        if (response.ok) {
                            button.closest('[data-row]')?.remove();
                            feedback.textContent = 'Pengumuman berhasil dihapus.';
                            feedback.classList.remove('d-none');
                            applyFilters();
                        } else {
                            showErrors(await response.json().catch(() => ({})));
                        }
                    });
                });
            };

            const showErrors = (payload) => {
                const messages = payload?.errors ? Object.values(payload.errors).flat() : [payload?.message || 'Proses gagal.'];
                errors.innerHTML = messages.map((message) => '<div>' + message + '</div>').join('');
                errors.classList.remove('d-none');
            };

            const resetForm = () => {
                form.reset();
                form.elements.id.value = '';
                errors.classList.add('d-none');
                feedback.classList.add('d-none');
                document.querySelector('[data-form-title]').textContent = 'Buat Pengumuman Baru';
                form.querySelectorAll('[name="target_roles[]"]').forEach(cb => {
                    cb.checked = false;
                });
            };

            document.querySelector('[data-create]').addEventListener('click', resetForm);
            bindRowActions(document);

            form.addEventListener('submit', async (event) => {
                event.preventDefault();
                const data = new FormData(form);
                const id = data.get('id');
                if (id) data.append('_method', 'PUT');

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
                    feedback.textContent = payload.message || 'Pengumuman berhasil disimpan.';
                    feedback.classList.remove('d-none');
                    applyFilters();
                } else {
                    showErrors(payload);
                }
            });

            const applyFilters = () => {
                const keyword = search.value.trim().toLowerCase();
                const rows = Array.from(document.querySelectorAll('[data-row]'));
                const matched = rows.filter((row) => row.dataset.searchText.includes(keyword));
                const totalPages = Math.max(Math.ceil(matched.length / pageSize), 1);
                currentPage = Math.min(currentPage, totalPages);
                rows.forEach((row) => row.classList.add('d-none'));
                matched.slice((currentPage - 1) * pageSize, currentPage * pageSize).forEach((row) => row.classList.remove('d-none'));
                emptyState.classList.toggle('d-none', matched.length > 0);
                pageInfo.textContent = matched.length + ' data | halaman ' + currentPage + ' dari ' + totalPages;
                prevButton.disabled = currentPage <= 1;
                nextButton.disabled = currentPage >= totalPages;
            };

            search.addEventListener('input', () => { currentPage = 1; applyFilters(); });
            prevButton.addEventListener('click', () => { currentPage -= 1; applyFilters(); });
            nextButton.addEventListener('click', () => { currentPage += 1; applyFilters(); });
            applyFilters();
        });
    </script>
@endpush
