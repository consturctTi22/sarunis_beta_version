<section class="portal-panel portal-attendance-report" id="laporan-tren" data-dashboard-section data-section-label="Laporan Statistik Absensi">
    <div class="portal-section-heading">
        <div>
            <h2>Laporan Statistik Absensi</h2>
            <p>Tren kehadiran mingguan dan bulanan, siswa perlu perhatian, kelas rendah hadir, serta rekap mapel/guru.</p>
        </div>
        <div class="portal-report-actions">
            <a class="btn btn-outline-primary btn-sm" href="{{ url('/admin/export/absensi/csv') }}">CSV Absensi</a>
            <a class="btn btn-outline-primary btn-sm" href="{{ url('/admin/export/absensi/xls') }}">Excel Absensi</a>
            <a class="btn btn-outline-primary btn-sm" href="{{ url('/admin/export/absensi/pdf') }}" target="_blank" rel="noopener">PDF Absensi</a>
            <a class="btn btn-outline-primary btn-sm" href="{{ url('/admin/export/catatan-siswa/csv') }}">CSV Catatan</a>
        </div>
    </div>

    <form class="portal-report-card portal-report-card--wide" method="GET" action="{{ url('/admin/export/absensi/csv') }}" data-admin-export-form>
        <div class="portal-report-card__header">
            <h3>Export Absensi Admin</h3>
            <span>Pilih konteks data</span>
        </div>
        <div class="row g-2 align-items-end">
            <div class="col-md-2">
                <label class="form-label" for="admin-export-type">Konteks</label>
                <select class="form-control" id="admin-export-type" name="type">
                    <option value="gabungan">Gabungan</option>
                    <option value="mapel">Mapel</option>
                    <option value="kelas">Kelas Perwalian</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label" for="admin-export-subject">Mapel</label>
                <select class="form-control" id="admin-export-subject" name="subject_id">
                    <option value="">Semua mapel</option>
                    @foreach (($adminExportSubjects ?? []) as $subjectOption)
                        <option value="{{ $subjectOption['id'] }}">{{ $subjectOption['name'] }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label" for="admin-export-class">Kelas</label>
                <select class="form-control" id="admin-export-class" name="school_class_id">
                    <option value="">Semua kelas</option>
                    @foreach (($adminExportClasses ?? []) as $classOption)
                        <option value="{{ $classOption['id'] }}">{{ $classOption['name'] }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label" for="admin-export-from">Dari</label>
                <input class="form-control" id="admin-export-from" type="date" name="date_from">
            </div>
            <div class="col-md-2">
                <label class="form-label" for="admin-export-to">Sampai</label>
                <input class="form-control" id="admin-export-to" type="date" name="date_to">
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button class="btn btn-primary btn-sm" type="submit" data-export-format="csv">CSV</button>
                <button class="btn btn-outline-primary btn-sm" type="submit" data-export-format="xls">Excel</button>
                <button class="btn btn-outline-primary btn-sm" type="submit" data-export-format="pdf">PDF</button>
            </div>
        </div>
    </form>

    <div class="portal-report-trend-grid">
        <article class="portal-report-card" data-search-item>
            <div class="portal-report-card__header">
                <h3>Kehadiran per Minggu</h3>
                <span>{{ count($attendanceReports['weekly_trends']) }} periode</span>
            </div>

            <div class="portal-report-bars">
                @forelse ($attendanceReports['weekly_trends'] as $trend)
                    <div class="portal-report-bar">
                        <div class="portal-report-bar__meta">
                            <span>{{ $trend['label'] }}</span>
                            <strong>{{ $trend['present_rate'] }}%</strong>
                        </div>
                        <div class="portal-report-bar__track">
                            <span style="width: {{ max($trend['present_rate'], 6) }}%"></span>
                        </div>
                        <small>{{ $trend['present'] }} hadir | {{ $trend['absent'] }} tidak hadir</small>
                    </div>
                @empty
                    <p class="portal-report-empty">Belum ada data tren mingguan.</p>
                @endforelse
            </div>
        </article>

        <article class="portal-report-card" data-search-item>
            <div class="portal-report-card__header">
                <h3>Kehadiran per Bulan</h3>
                <span>{{ count($attendanceReports['monthly_trends']) }} periode</span>
            </div>

            <div class="portal-report-bars">
                @forelse ($attendanceReports['monthly_trends'] as $trend)
                    <div class="portal-report-bar">
                        <div class="portal-report-bar__meta">
                            <span>{{ $trend['label'] }}</span>
                            <strong>{{ $trend['present_rate'] }}%</strong>
                        </div>
                        <div class="portal-report-bar__track">
                            <span style="width: {{ max($trend['present_rate'], 6) }}%"></span>
                        </div>
                        <small>{{ $trend['present'] }} hadir | {{ $trend['absent'] }} tidak hadir</small>
                    </div>
                @empty
                    <p class="portal-report-empty">Belum ada data tren bulanan.</p>
                @endforelse
            </div>
        </article>
    </div>

    <div class="portal-report-list-grid">
        <article class="portal-report-card" data-search-item>
            <div class="portal-report-card__header">
                <h3>Siswa Sering Alpha/Sakit/Izin</h3>
                <span>Top {{ count($attendanceReports['top_absent_students']) }}</span>
            </div>

            <div class="portal-report-list">
                @forelse ($attendanceReports['top_absent_students'] as $student)
                    <div class="portal-report-row">
                        <div>
                            <strong>{{ $student['student'] }}</strong>
                            <small>{{ $student['class_name'] }}</small>
                        </div>
                        <span>{{ $student['total'] }} kasus</span>
                        <small>A {{ $student['alpha'] }} | S {{ $student['sakit'] }} | I {{ $student['izin'] }}</small>
                    </div>
                @empty
                    <p class="portal-report-empty">Belum ada siswa dengan catatan tidak hadir.</p>
                @endforelse
            </div>
        </article>

        <article class="portal-report-card" data-search-item>
            <div class="portal-report-card__header">
                <h3>Kelas Hadir Rendah</h3>
                <span>{{ $attendanceReports['effective_days'] }} hari efektif</span>
            </div>

            <div class="portal-report-list">
                @forelse ($attendanceReports['low_attendance_classes'] as $classReport)
                    <div class="portal-report-row">
                        <div>
                            <strong>{{ $classReport['class_name'] }}</strong>
                            <small>{{ $classReport['homeroom_teacher'] }}</small>
                        </div>
                        <span>{{ $classReport['present_rate'] }}%</span>
                        <small>{{ $classReport['present'] }} hadir | {{ $classReport['absent'] }} tidak hadir</small>
                    </div>
                @empty
                    <p class="portal-report-empty">Belum ada data kelas yang bisa dihitung.</p>
                @endforelse
            </div>
        </article>
    </div>

    <div class="portal-report-card portal-report-card--wide" data-search-item>
        <div class="portal-report-card__header">
            <h3>Rekap Mapel/Guru</h3>
            <span>{{ count($attendanceReports['subject_teacher_recaps']) }} jadwal</span>
        </div>

        <div class="table-responsive">
            <table class="table portal-table mb-0">
                <thead>
                    <tr>
                        <th>Mapel</th>
                        <th>Guru</th>
                        <th>Kelas</th>
                        <th>Hadir</th>
                        <th>Alpha</th>
                        <th>Sakit</th>
                        <th>Izin</th>
                        <th>% Hadir</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($attendanceReports['subject_teacher_recaps'] as $recap)
                        <tr>
                            <td>{{ $recap['subject'] }}</td>
                            <td>{{ $recap['teacher'] }}</td>
                            <td>{{ $recap['class_name'] }}</td>
                            <td>{{ $recap['present'] }}</td>
                            <td>{{ $recap['alpha'] }}</td>
                            <td>{{ $recap['sakit'] }}</td>
                            <td>{{ $recap['izin'] }}</td>
                            <td>{{ $recap['present_rate'] }}%</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">Belum ada rekap mapel/guru untuk ditampilkan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</section>

<script>
    document.querySelectorAll('[data-admin-export-form]').forEach(function (form) {
        form.querySelectorAll('[data-export-format]').forEach(function (button) {
            button.addEventListener('click', function () {
                form.action = '/admin/export/absensi/' + button.dataset.exportFormat;
                form.target = button.dataset.exportFormat === 'pdf' ? '_blank' : '';
            });
        });
    });
</script>
