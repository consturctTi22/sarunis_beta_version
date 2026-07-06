<div class="modal fade" id="note-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content portal-directory-modal">
            <form data-note-form>
                <div class="modal-header border-0 pb-0">
                    <h2 class="modal-title fs-4 fw-bold" data-form-title>Tambah Catatan</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-danger d-none" data-errors></div>
                    <input type="hidden" name="id">
                    <div class="mb-3">
                        <label class="form-label">Siswa</label>
                        <select class="form-select" name="student_id" required>
                            <option value="">Pilih siswa</option>
                            @foreach ($students as $student)
                                <option value="{{ $student->id }}">{{ $student->name }}{{ $student->schoolClass ? ' - '.$student->schoolClass->name : '' }}</option>
                            @endforeach
                        </select>
                    </div>
                    @if ($showTeacherField)
                        <div class="mb-3">
                            <label class="form-label">Guru terkait</label>
                            <select class="form-select" name="teacher_id">
                                <option value="">Tidak ada</option>
                                @foreach ($teachers as $teacher)
                                    <option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label">Judul</label>
                            <input class="form-control" name="title" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Kategori</label>
                            <input class="form-control" name="category" value="umum" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Catatan</label>
                            <textarea class="form-control" name="note" rows="4" required></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tanggal tindak lanjut</label>
                            <input class="form-control" name="follow_up_at" type="date">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tanggal selesai</label>
                            <input class="form-control" name="resolved_at" type="date">
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
