<section class="portal-directory-section">
    <div class="table-responsive">
        <table class="table portal-table portal-directory-table mb-0">
            <thead>
                <tr>
                    <th>Siswa</th>
                    <th>Kelas</th>
                    <th>Judul</th>
                    <th>Kategori</th>
                    <th>Guru/User</th>
                    <th>Tindak Lanjut</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($notes as $note)
                    @php
                        $notePayload = [
                            'id' => $note->id,
                            'student_id' => $note->student_id,
                            'teacher_id' => $note->teacher_id,
                            'title' => $note->title,
                            'category' => $note->category,
                            'note' => $note->note,
                            'follow_up_at' => $note->follow_up_at?->format('Y-m-d'),
                            'resolved_at' => $note->resolved_at?->format('Y-m-d'),
                        ];
                    @endphp
                    <tr data-row data-row-id="{{ $note->id }}" data-category="{{ $note->category }}" data-status="{{ $note->resolved_at ? 'resolved' : 'open' }}" data-search-text="{{ mb_strtolower(($note->student?->name ?? '').' '.($note->title ?? '').' '.($note->category ?? '').' '.($note->note ?? '')) }}">
                        <td>{{ $note->student?->name ?? '-' }}</td>
                        <td>{{ $note->student?->schoolClass?->name ?? '-' }}</td>
                        <td>{{ $note->title }}</td>
                        <td>{{ $note->category }}</td>
                        <td>{{ $note->teacher?->name ?? $note->user?->name ?? '-' }}</td>
                        <td>{{ $note->follow_up_at?->format('d-m-Y') ?? '-' }}</td>
                        <td>{{ $note->resolved_at ? 'Selesai' : 'Terbuka' }}</td>
                        <td>
                            <div class="portal-directory-actions">
                                <button class="portal-directory-action is-edit" type="button" data-edit='@json($notePayload)'>@include('dashboard.partials.icon', ['name' => 'edit'])</button>
                                <button class="portal-directory-action is-delete" type="button" data-delete="{{ $note->id }}" data-name="{{ $note->title }}">@include('dashboard.partials.icon', ['name' => 'trash'])</button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr data-empty>
                        <td colspan="8">Belum ada catatan siswa.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="portal-directory-feedback d-none" data-empty-state>Tidak ada catatan yang cocok.</div>
    <div class="d-flex justify-content-between align-items-center gap-3 p-3">
        <button class="btn btn-light btn-sm" type="button" data-prev>Prev</button>
        <span class="small text-muted" data-page-info></span>
        <button class="btn btn-light btn-sm" type="button" data-next>Next</button>
    </div>
</section>
