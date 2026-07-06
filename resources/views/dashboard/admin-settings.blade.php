@extends('layouts.portal-dashboard')

@section('title', $pageTitle)

@section('content')
    <div class="portal-dashboard-shell portal-directory-shell" data-settings-page data-endpoint="{{ url('/admin/setting') }}">
        @include('dashboard.partials.sidebar', ['menuSections' => $menuSections])

        <main class="portal-dashboard-main portal-directory-main">
            <div class="portal-directory-stack">
                <section class="portal-panel portal-directory-banner">
                    <div class="portal-directory-banner__bar"></div>
                    <div class="portal-directory-banner__copy">
                        <h1>Pengaturan</h1>
                        <p>Kelola konfigurasi aplikasi seperti nama sekolah, tahun ajaran, dan kontak.</p>
                    </div>
                    <div class="portal-directory-banner__count">{{ $settings->count() }} item</div>
                </section>

                <section class="portal-directory-toolbar">
                    <label class="portal-directory-search">
                        <span class="portal-directory-search__icon">@include('dashboard.partials.icon', ['name' => 'search'])</span>
                        <input type="search" placeholder="Pencarian..." data-search>
                    </label>
                    <label class="portal-directory-filter">
                        <select data-type-filter>
                            <option value="">Semua tipe</option>
                            @foreach (['text', 'number', 'boolean', 'textarea', 'email', 'url'] as $type)
                                <option value="{{ $type }}">{{ $type }}</option>
                            @endforeach
                        </select>
                    </label>
                    <button class="portal-round-action" type="button" data-create data-bs-toggle="modal" data-bs-target="#setting-modal">@include('dashboard.partials.icon', ['name' => 'plus'])</button>
                </section>

                <div class="alert alert-success d-none" data-feedback></div>

                <section class="portal-directory-section">
                    <div class="table-responsive">
                        <table class="table portal-table portal-directory-table mb-0">
                            <thead>
                                <tr>
                                    <th>Key</th>
                                    <th>Label</th>
                                    <th>Nilai</th>
                                    <th>Tipe</th>
                                    <th>Deskripsi</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($settings as $setting)
                                    <tr data-row data-row-id="{{ $setting->id }}" data-type="{{ $setting->type }}" data-search-text="{{ mb_strtolower($setting->key.' '.$setting->label.' '.$setting->value) }}">
                                        <td>{{ $setting->key }}</td>
                                        <td>{{ $setting->label }}</td>
                                        <td>{{ $setting->value ?: '-' }}</td>
                                        <td>{{ $setting->type }}</td>
                                        <td>{{ $setting->description ?: '-' }}</td>
                                        <td>
                                            <div class="portal-directory-actions">
                                                <button class="portal-directory-action is-edit" type="button" data-edit='@json($setting)'>@include('dashboard.partials.icon', ['name' => 'edit'])</button>
                                                <button class="portal-directory-action is-delete" type="button" data-delete="{{ $setting->id }}" data-name="{{ $setting->label }}">@include('dashboard.partials.icon', ['name' => 'trash'])</button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="portal-directory-feedback d-none" data-empty-state>Tidak ada pengaturan yang cocok.</div>
                    <div class="d-flex justify-content-between align-items-center gap-3 p-3">
                        <button class="btn btn-light btn-sm" type="button" data-prev>Prev</button>
                        <span class="small text-muted" data-page-info></span>
                        <button class="btn btn-light btn-sm" type="button" data-next>Next</button>
                    </div>
                </section>
            </div>
        </main>
    </div>

    <div class="modal fade" id="setting-modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content portal-directory-modal">
                <form data-setting-form>
                    <div class="modal-header border-0 pb-0">
                        <h2 class="modal-title fs-4 fw-bold" data-form-title>Tambah Pengaturan</h2>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-danger d-none" data-errors></div>
                        <input type="hidden" name="id">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Key</label>
                                <input class="form-control" name="key" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Tipe</label>
                                <select class="form-select" name="type" required>
                                    @foreach (['text', 'number', 'boolean', 'textarea', 'email', 'url'] as $type)
                                        <option value="{{ $type }}">{{ $type }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Label</label>
                                <input class="form-control" name="label" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Nilai</label>
                                <textarea class="form-control" name="value" rows="3"></textarea>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Deskripsi</label>
                                <textarea class="form-control" name="description" rows="2"></textarea>
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
            const page = document.querySelector('[data-settings-page]');
            const form = document.querySelector('[data-setting-form]');
            const modal = window.bootstrap.Modal.getOrCreateInstance(document.getElementById('setting-modal'));
            const errors = document.querySelector('[data-errors]');
            const feedback = document.querySelector('[data-feedback]');
            const search = document.querySelector('[data-search]');
            const typeFilter = document.querySelector('[data-type-filter]');
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
            const rowHtml = function (setting) {
                const payload = escapeHtml(JSON.stringify(setting));
                const searchText = escapeHtml(((setting.key || '') + ' ' + (setting.label || '') + ' ' + (setting.value || '')).toLowerCase());

                return '' +
                    '<tr data-row data-row-id="' + escapeHtml(setting.id) + '" data-type="' + escapeHtml(setting.type) + '" data-search-text="' + searchText + '">' +
                        '<td>' + escapeHtml(setting.key) + '</td>' +
                        '<td>' + escapeHtml(setting.label) + '</td>' +
                        '<td>' + escapeHtml(setting.value || '-') + '</td>' +
                        '<td>' + escapeHtml(setting.type) + '</td>' +
                        '<td>' + escapeHtml(setting.description || '-') + '</td>' +
                        '<td><div class="portal-directory-actions">' +
                            '<button class="portal-directory-action is-edit" type="button" data-edit=\'' + payload + '\'>' + editIcon + '</button>' +
                            '<button class="portal-directory-action is-delete" type="button" data-delete="' + escapeHtml(setting.id) + '" data-name="' + escapeHtml(setting.label) + '">' + deleteIcon + '</button>' +
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
                        document.querySelector('[data-form-title]').textContent = 'Ubah Pengaturan';
                        ['id', 'key', 'label', 'value', 'type', 'description'].forEach((field) => form.elements[field].value = data[field] || '');
                        modal.show();
                    });
                });
                root.querySelectorAll('[data-delete]').forEach((button) => {
                    if (button.dataset.bound === '1') return;
                    button.dataset.bound = '1';
                    button.addEventListener('click', async () => {
                        if (!confirm('Hapus pengaturan ' + button.dataset.name + '?')) return;
                        const response = await fetch(page.dataset.endpoint + '/' + button.dataset.delete, {
                            method: 'POST',
                            headers: {'X-CSRF-TOKEN': token, 'Accept': 'application/json'},
                            body: new URLSearchParams({'_method': 'DELETE'}),
                        });
                        if (response.ok) {
                            button.closest('[data-row]')?.remove();
                            feedback.textContent = 'Pengaturan berhasil dihapus.';
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
                document.querySelector('[data-form-title]').textContent = 'Tambah Pengaturan';
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
                    feedback.textContent = payload.message || 'Pengaturan berhasil disimpan.';
                    feedback.classList.remove('d-none');
                    applyFilters();
                } else {
                    showErrors(payload);
                }
            });
            const applyFilters = () => {
                const keyword = search.value.trim().toLowerCase();
                const type = typeFilter.value;
                const rows = Array.from(document.querySelectorAll('[data-row]'));
                const matched = rows.filter((row) => row.dataset.searchText.includes(keyword) && (!type || row.dataset.type === type));
                const totalPages = Math.max(Math.ceil(matched.length / pageSize), 1);
                currentPage = Math.min(currentPage, totalPages);
                rows.forEach((row) => row.classList.add('d-none'));
                matched.slice((currentPage - 1) * pageSize, currentPage * pageSize).forEach((row) => row.classList.remove('d-none'));
                emptyState.classList.toggle('d-none', matched.length > 0);
                pageInfo.textContent = matched.length + ' data | halaman ' + currentPage + ' dari ' + totalPages;
                prevButton.disabled = currentPage <= 1;
                nextButton.disabled = currentPage >= totalPages;
            };
            [search, typeFilter].forEach((input) => input.addEventListener('input', () => { currentPage = 1; applyFilters(); }));
            prevButton.addEventListener('click', () => { currentPage -= 1; applyFilters(); });
            nextButton.addEventListener('click', () => { currentPage += 1; applyFilters(); });
            applyFilters();
        });
    </script>
@endpush
