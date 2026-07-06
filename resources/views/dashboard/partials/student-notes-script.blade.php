@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const page = document.querySelector('[data-notes-page]');
            const form = document.querySelector('[data-note-form]');
            const modal = window.bootstrap.Modal.getOrCreateInstance(document.getElementById('note-modal'));
            const errors = document.querySelector('[data-errors]');
            const feedback = document.querySelector('[data-feedback]');
            const search = document.querySelector('[data-search]');
            const statusFilter = document.querySelector('[data-status-filter]');
            const categoryFilter = document.querySelector('[data-category-filter]');
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
            const dateOnly = function (value) {
                return value ? String(value).slice(0, 10) : '';
            };
            const formatDate = function (value) {
                const date = dateOnly(value);
                if (!date) return '-';
                const parts = date.split('-');
                return parts.length === 3 ? parts[2] + '-' + parts[1] + '-' + parts[0] : date;
            };
            const notePayload = function (note) {
                return {
                    id: note.id,
                    student_id: note.student_id,
                    teacher_id: note.teacher_id,
                    title: note.title,
                    category: note.category,
                    note: note.note,
                    follow_up_at: dateOnly(note.follow_up_at),
                    resolved_at: dateOnly(note.resolved_at),
                };
            };
            const rowHtml = function (note) {
                const payload = escapeHtml(JSON.stringify(notePayload(note)));
                const studentName = note.student?.name || '-';
                const className = note.student?.school_class?.name || '-';
                const actorName = note.teacher?.name || note.user?.name || '-';
                const status = note.resolved_at ? 'resolved' : 'open';
                const searchText = escapeHtml((studentName + ' ' + note.title + ' ' + note.category + ' ' + note.note).toLowerCase());

                return '' +
                    '<tr data-row data-row-id="' + escapeHtml(note.id) + '" data-category="' + escapeHtml(note.category) + '" data-status="' + status + '" data-search-text="' + searchText + '">' +
                        '<td>' + escapeHtml(studentName) + '</td>' +
                        '<td>' + escapeHtml(className) + '</td>' +
                        '<td>' + escapeHtml(note.title) + '</td>' +
                        '<td>' + escapeHtml(note.category) + '</td>' +
                        '<td>' + escapeHtml(actorName) + '</td>' +
                        '<td>' + escapeHtml(formatDate(note.follow_up_at)) + '</td>' +
                        '<td>' + (note.resolved_at ? 'Selesai' : 'Terbuka') + '</td>' +
                        '<td><div class="portal-directory-actions">' +
                            '<button class="portal-directory-action is-edit" type="button" data-edit=\'' + payload + '\'>' + editIcon + '</button>' +
                            '<button class="portal-directory-action is-delete" type="button" data-delete="' + escapeHtml(note.id) + '" data-name="' + escapeHtml(note.title) + '">' + deleteIcon + '</button>' +
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
                        document.querySelector('[data-form-title]').textContent = 'Ubah Catatan';
                        ['id', 'student_id', 'teacher_id', 'title', 'category', 'note', 'follow_up_at', 'resolved_at'].forEach((field) => {
                            if (form.elements[field]) form.elements[field].value = data[field] || '';
                        });
                        modal.show();
                    });
                });
                root.querySelectorAll('[data-delete]').forEach((button) => {
                    if (button.dataset.bound === '1') return;
                    button.dataset.bound = '1';
                    button.addEventListener('click', async () => {
                        if (!confirm('Hapus catatan ' + button.dataset.name + '?')) return;
                        const response = await fetch(page.dataset.endpoint + '/' + button.dataset.delete, {
                            method: 'POST',
                            headers: {'X-CSRF-TOKEN': token, 'Accept': 'application/json'},
                            body: new URLSearchParams({'_method': 'DELETE'}),
                        });
                        if (response.ok) {
                            button.closest('[data-row]')?.remove();
                            feedback.textContent = 'Catatan siswa berhasil dihapus.';
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
                if (form.elements.category) form.elements.category.value = 'umum';
                errors.classList.add('d-none');
                feedback.classList.add('d-none');
                document.querySelector('[data-form-title]').textContent = 'Tambah Catatan';
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
                    feedback.textContent = payload.message || 'Catatan siswa berhasil disimpan.';
                    feedback.classList.remove('d-none');
                    applyFilters();
                } else {
                    showErrors(payload);
                }
            });
            const applyFilters = () => {
                const keyword = search.value.trim().toLowerCase();
                const status = statusFilter.value;
                const category = categoryFilter.value;
                const rows = Array.from(document.querySelectorAll('[data-row]'));
                const matched = rows.filter((row) => row.dataset.searchText.includes(keyword)
                    && (!status || row.dataset.status === status)
                    && (!category || row.dataset.category === category));
                const totalPages = Math.max(Math.ceil(matched.length / pageSize), 1);
                currentPage = Math.min(currentPage, totalPages);
                rows.forEach((row) => row.classList.add('d-none'));
                matched.slice((currentPage - 1) * pageSize, currentPage * pageSize).forEach((row) => row.classList.remove('d-none'));
                emptyState.classList.toggle('d-none', matched.length > 0);
                pageInfo.textContent = matched.length + ' data | halaman ' + currentPage + ' dari ' + totalPages;
                prevButton.disabled = currentPage <= 1;
                nextButton.disabled = currentPage >= totalPages;
            };
            [search, statusFilter, categoryFilter].forEach((input) => input.addEventListener('input', () => { currentPage = 1; applyFilters(); }));
            prevButton.addEventListener('click', () => { currentPage -= 1; applyFilters(); });
            nextButton.addEventListener('click', () => { currentPage += 1; applyFilters(); });
            applyFilters();
        });
    </script>
@endpush
