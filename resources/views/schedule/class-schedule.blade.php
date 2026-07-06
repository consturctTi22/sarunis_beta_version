@extends('layouts.portal-dashboard')

@section('title', 'Jadwal Kelas ' . $class->name)

@php
    $menuSections = [
        [
            'title' => 'Menu',
            'items' => [
                ['label' => 'Beranda', 'icon' => 'home', 'href' => url('/admin/dashboard'), 'active' => false],
                ['label' => 'Data Siswa', 'icon' => 'students', 'href' => url('/admin/data-siswa'), 'active' => false],
                ['label' => 'Data Guru', 'icon' => 'teacher', 'href' => url('/admin/data-guru'), 'active' => false],
                ['label' => 'Data Kelas', 'icon' => 'class', 'href' => url('/admin/data-kelas'), 'active' => false],
                ['label' => 'Mata Pelajaran', 'icon' => 'subject', 'href' => url('/admin/mata-pelajaran'), 'active' => false],
                ['label' => 'Kalender Akademik', 'icon' => 'calendar', 'href' => url('/admin/kalender-akademik'), 'active' => false],
                ['label' => 'Jadwal Pelajaran', 'icon' => 'schedule', 'href' => url('/admin/schedule/generate'), 'active' => true],
                ['label' => 'Rekap Kehadiran', 'icon' => 'recap', 'href' => url('/admin/rekap-kehadiran'), 'active' => false],
                ['label' => 'Laporan Statistik', 'icon' => 'chart', 'href' => url('/admin/laporan-statistik'), 'active' => false],
            ],
        ],
        [
            'title' => 'Lainnya',
            'items' => [
                ['label' => 'Manajemen Pengguna', 'icon' => 'users', 'href' => url('/admin/manajemen-pengguna'), 'active' => false],
                ['label' => 'Pengaturan', 'icon' => 'settings', 'href' => url('/admin/pengaturan'), 'active' => false],
                ['label' => 'Catatan Siswa', 'icon' => 'note', 'href' => url('/admin/catatan-siswa'), 'active' => false],
            ],
        ],
    ];
@endphp

@section('content')
    <div class="portal-dashboard-shell portal-directory-shell">
        @include('dashboard.partials.sidebar', ['menuSections' => $menuSections])

        <main class="portal-dashboard-main portal-directory-main">
            <div class="portal-directory-stack">
                <section class="portal-panel portal-directory-banner">
                    <div class="portal-directory-banner__bar"></div>
                    <div class="portal-directory-banner__copy">
                        <h1>Jadwal Pelajaran Kelas {{ $class->name }}</h1>
                        <p>Tahun Ajaran {{ $academicYear }} | Tingkat {{ $class->level }}</p>
                    </div>
                    <div class="portal-directory-banner__count">{{ $class->name }}</div>
                </section>

                <section class="portal-panel p-4 mb-4">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                        <div>
                            <h2 class="fs-5 fw-bold mb-1">Weekly Schedule Matrix</h2>
                            <p class="text-secondary mb-0">Klik tombol di sebelah kanan untuk mengekspor jadwal ini dalam format kalender iCal atau dokumen.</p>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ url("/admin/schedule/export/{$class->id}/{$academicYear}/html") }}" class="btn btn-sm btn-outline-primary d-flex align-items-center gap-1">
                                <span>📥</span> HTML
                            </a>
                            <a href="{{ url("/admin/schedule/export/{$class->id}/{$academicYear}/csv") }}" class="btn btn-sm btn-outline-primary d-flex align-items-center gap-1">
                                <span>📥</span> CSV
                            </a>
                            <a href="{{ url("/admin/schedule/export/{$class->id}/{$academicYear}/ics") }}" class="btn btn-sm btn-outline-success d-flex align-items-center gap-1" title="Impor ke Google Calendar / Apple Calendar">
                                <span>📅</span> iCal (ICS)
                            </a>
                            <a href="{{ url('/admin/schedule/generate') }}" class="btn btn-sm btn-secondary">
                                Kembali
                            </a>
                        </div>
                    </div>
                </section>

                <div class="row row-cols-1 row-cols-md-2 row-cols-xl-5 g-3">
                    @foreach ($schedule['schedule'] as $day => $sessions)
                        <div class="col">
                            <section class="portal-panel h-100 p-3">
                                <div class="border-bottom pb-2 mb-3">
                                    <h3 class="fs-6 fw-bold text-primary mb-0">{{ $day }}</h3>
                                    <small class="text-secondary">{{ count($sessions) }} Sesi</small>
                                </div>

                                <div class="d-flex flex-column gap-3">
                                    @forelse ($sessions as $session)
                                        <div class="p-3 bg-light rounded border-start border-primary border-4 position-relative">
                                            <div class="fw-bold fs-7 text-dark mb-1">{{ $session['subject'] }}</div>
                                            <div class="text-secondary fs-8 mb-1">{{ $session['teacher'] }}</div>
                                            
                                            @if (!empty($session['substitute_teacher']))
                                                <div class="text-warning fs-8 mb-2" style="font-weight: 500;">
                                                    🛡️ Piket: {{ $session['substitute_teacher'] }}
                                                </div>
                                            @endif

                                            <div class="d-flex justify-content-between text-secondary fs-9 border-top pt-2">
                                                <span>⏰ {{ $session['time'] }}</span>
                                                <span class="fw-semibold">📍 {{ $session['room'] }}</span>
                                            </div>

                                            <button class="btn btn-xs btn-outline-secondary position-absolute top-0 end-0 m-1 py-0 px-1 border-0" 
                                                    style="font-size: 0.75rem; background: transparent; opacity: 0.6;"
                                                    type="button" 
                                                    data-edit-schedule='@json($session)' 
                                                    title="Transfer Guru / Guru Piket">
                                                ✏️
                                            </button>
                                        </div>
                                    @empty
                                        <div class="text-center py-5 text-secondary fs-8">
                                            <em>Tidak ada jadwal</em>
                                        </div>
                                    @endforelse
                                </div>
                            </section>
                        </div>
                    @endforeach
                </div>
            </div>
        </main>
    </div>

    <!-- Modal Edit Jadwal -->
    <div class="modal fade" id="edit-schedule-modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content portal-directory-modal">
                <form id="edit-schedule-form">
                    <div class="modal-header border-0 pb-0">
                        <h2 class="modal-title fs-5 fw-bold">Edit Jadwal Ajar</h2>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-danger d-none" id="modal-errors"></div>
                        <input type="hidden" name="id">
                        <input type="hidden" name="subject_id">
                        <input type="hidden" name="school_class_id">
                        <input type="hidden" name="academic_year">
                        <input type="hidden" name="day_of_week">
                        <input type="hidden" name="start_time">
                        <input type="hidden" name="end_time">
                        
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-semibold">Mata Pelajaran</label>
                                <input class="form-control bg-light" id="display-subject" readonly>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Waktu</label>
                                <input class="form-control bg-light" id="display-time" readonly>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold" for="edit-teacher-id">Guru Utama (Transfer Jadwal)</label>
                                <select class="form-select" id="edit-teacher-id" name="teacher_id" required>
                                    @foreach ($teachers as $t)
                                        <option value="{{ $t->id }}">{{ $t->name }}</option>
                                    @endforeach
                                </select>
                                <small class="text-secondary d-block mt-1">Ganti guru utama untuk mentransfer jadwal pelajaran ini.</small>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold" for="edit-substitute-id">Guru Piket / Pengganti</label>
                                <select class="form-select" id="edit-substitute-id" name="substitute_teacher_id">
                                    <option value="">-- Tanpa Guru Piket --</option>
                                    @foreach ($teachers as $t)
                                        <option value="{{ $t->id }}">{{ $t->name }}</option>
                                    @endforeach
                                </select>
                                <small class="text-secondary d-block mt-1">Tentukan guru piket/pengganti jika guru utama berhalangan hadir.</small>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold" for="edit-room">Ruangan</label>
                                <input class="form-control" id="edit-room" name="room" placeholder="Contoh: Ruang X IPA 1">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const modalEl = document.getElementById('edit-schedule-modal');
            const modal = window.bootstrap.Modal.getOrCreateInstance(modalEl);
            const form = document.getElementById('edit-schedule-form');
            const errorsEl = document.getElementById('modal-errors');
            const token = '{{ csrf_token() }}';

            document.querySelectorAll('[data-edit-schedule]').forEach(function (button) {
                button.addEventListener('click', function () {
                    const data = JSON.parse(button.dataset.editSchedule);
                    errorsEl.classList.add('d-none');
                    
                    form.elements.id.value = data.id || '';
                    form.elements.subject_id.value = data.subject_id || '';
                    form.elements.school_class_id.value = data.school_class_id || '';
                    form.elements.academic_year.value = data.academic_year || '';
                    form.elements.day_of_week.value = data.day_of_week ?? '';
                    form.elements.start_time.value = data.start_time || '';
                    form.elements.end_time.value = data.end_time || '';
                    
                    document.getElementById('display-subject').value = data.subject || '';
                    document.getElementById('display-time').value = data.time || '';
                    
                    form.elements.teacher_id.value = data.teacher_id || '';
                    form.elements.substitute_teacher_id.value = data.substitute_teacher_id || '';
                    form.elements.room.value = data.room || '';
                    
                    modal.show();
                });
            });

            form.addEventListener('submit', async function (event) {
                event.preventDefault();
                errorsEl.classList.add('d-none');
                
                const assignmentId = form.elements.id.value;
                const data = new FormData(form);
                data.append('_method', 'PUT');

                try {
                    const response = await fetch('/admin/jadwal-ajar/' + assignmentId, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': token,
                            'Accept': 'application/json'
                        },
                        body: data
                    });

                    const payload = await response.json().catch(() => ({}));
                    
                    if (response.ok) {
                        modal.hide();
                        window.location.reload();
                    } else {
                        const messages = payload?.errors ? Object.values(payload.errors).flat() : [payload?.message || 'Proses gagal.'];
                        errorsEl.innerHTML = messages.map((message) => '<div>' + message + '</div>').join('');
                        errorsEl.classList.remove('d-none');
                    }
                } catch (error) {
                    errorsEl.textContent = 'Terjadi kesalahan koneksi.';
                    errorsEl.classList.remove('d-none');
                }
            });
        });
    </script>
@endpush
