@php
    $recapSectionId = $recapSectionId ?? 'rekap-absensi';
    $recapSectionLabel = $recapSectionLabel ?? 'Rekap Absensi Kelas';
    $recapTitle = $recapTitle ?? 'Rekap Absensi Kelas';
    $recapDescription = $recapDescription ?? 'Data absensi harian dari kelas perwalian.';
    $recapExportBaseUrl = $recapExportBaseUrl ?? url('/walikelas/rekap-absensi');
    $recapFilters = $recapFilters ?? ($attendanceFilters ?? []);
    $exportQuery = request()->getQueryString();
    $withExportQuery = fn (string $url): string => $exportQuery ? $url . '?' . $exportQuery : $url;
    $recapExportXlsUrl = $recapExportXlsUrl ?? $recapExportBaseUrl . '/export/xls';
    $recapExportCsvUrl = $recapExportCsvUrl ?? $recapExportBaseUrl . '/export/csv';
    $recapExportPdfUrl = $recapExportPdfUrl ?? $recapExportBaseUrl . '/export/pdf';
    $recapPrintUrl = $recapPrintUrl ?? $recapExportBaseUrl . '/print';
@endphp

<section class="portal-panel portal-teacher-recap" id="{{ $recapSectionId }}" data-dashboard-section data-section-label="{{ $recapSectionLabel }}">
    <div class="portal-section-heading">
        <div>
            <h2>{{ $recapTitle }}</h2>
            <p>{{ $recapDescription }} Data terakhir {{ $classAttendanceSummary['latest_date'] }} dengan persentase hadir {{ $classAttendanceSummary['present_rate'] }}%.</p>
        </div>
        <div class="portal-report-actions">
            <a class="btn btn-outline-primary btn-sm" href="{{ $withExportQuery($recapExportXlsUrl) }}">Excel Kelas</a>
            <a class="btn btn-outline-primary btn-sm" href="{{ $withExportQuery($recapExportCsvUrl) }}">CSV Kelas</a>
            <a class="btn btn-outline-primary btn-sm" href="{{ $withExportQuery($recapExportPdfUrl) }}" target="_blank" rel="noopener">PDF Kelas</a>
            <a class="btn btn-outline-primary btn-sm" href="{{ $withExportQuery($recapPrintUrl) }}" target="_blank" rel="noopener">Print</a>
        </div>
    </div>

    <form class="portal-report-card portal-report-card--wide" method="GET" action="{{ url()->current() }}">
        <div class="portal-report-card__header">
            <h3>Filter Export Kelas Perwalian</h3>
            <span>{{ count($classAttendanceDetailRows) }} detail</span>
        </div>
        <div class="row g-2 align-items-end">
            <div class="col-md-4">
                <label class="form-label" for="{{ $recapSectionId }}-class">Kelas Perwalian</label>
                <select class="form-control" id="{{ $recapSectionId }}-class" name="school_class_id">
                    <option value="">Semua kelas perwalian</option>
                    @foreach (($homeroomExportClasses ?? []) as $classOption)
                        <option value="{{ $classOption['id'] }}" @selected((string) ($recapFilters['school_class_id'] ?? '') === (string) $classOption['id'])>{{ $classOption['name'] }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label" for="{{ $recapSectionId }}-from">Dari</label>
                <input class="form-control" id="{{ $recapSectionId }}-from" type="date" name="date_from" value="{{ $recapFilters['date_from'] ?? '' }}">
            </div>
            <div class="col-md-3">
                <label class="form-label" for="{{ $recapSectionId }}-to">Sampai</label>
                <input class="form-control" id="{{ $recapSectionId }}-to" type="date" name="date_to" value="{{ $recapFilters['date_to'] ?? '' }}">
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button class="btn btn-primary btn-sm" type="submit">Terapkan</button>
                <a class="btn btn-outline-primary btn-sm" href="{{ url()->current() }}">Reset</a>
            </div>
        </div>
    </form>

    <div class="portal-teacher-recap__summary">
        <article>
            <span>Total Catatan</span>
            <strong>{{ $classAttendanceSummary['total'] }}</strong>
        </article>
        <article>
            <span>Hadir</span>
            <strong>{{ $classAttendanceSummary['hadir'] }}</strong>
        </article>
        <article>
            <span>Izin</span>
            <strong>{{ $classAttendanceSummary['izin'] }}</strong>
        </article>
        <article>
            <span>Sakit</span>
            <strong>{{ $classAttendanceSummary['sakit'] }}</strong>
        </article>
        <article>
            <span>Alpha</span>
            <strong>{{ $classAttendanceSummary['alpha'] }}</strong>
        </article>
    </div>

    <div class="portal-report-card portal-report-card--wide">
        <div class="portal-report-card__header">
            <h3>Rekap Per Kelas</h3>
            <span>{{ count($classAttendanceRecapRows) }} kelas</span>
        </div>

        <div class="table-responsive">
            <table class="table portal-table mb-0">
                <thead>
                    <tr>
                        <th>Kelas</th>
                        <th>Pertemuan</th>
                        <th>Hadir</th>
                        <th>Izin</th>
                        <th>Sakit</th>
                        <th>Alpha</th>
                        <th>% Hadir</th>
                        <th>Terakhir</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($classAttendanceRecapRows as $row)
                    <tr data-search-item>
                        <td>{{ $row['class_name'] }}</td>
                        <td>{{ $row['dates_count'] }}</td>
                        <td>{{ $row['hadir'] }}</td>
                        <td>{{ $row['izin'] }}</td>
                        <td>{{ $row['sakit'] }}</td>
                        <td>{{ $row['alpha'] }}</td>
                        <td><span class="portal-badge is-primary">{{ $row['present_rate'] }}%</span></td>
                        <td>{{ $row['latest_date'] }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">Belum ada data absensi kelas.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="portal-report-card portal-report-card--wide">
        <div class="portal-report-card__header">
            <h3>Detail Catatan Siswa</h3>
            <span>{{ count($classAttendanceDetailRows) }} baris</span>
        </div>

        <div class="table-responsive">
            <table class="table portal-table mb-0">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Kelas</th>
                        <th>Siswa</th>
                        <th>Status</th>
                        <th>Catatan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($classAttendanceDetailRows as $row)
                    <tr data-search-item>
                        <td>{{ $row['date'] }}</td>
                        <td>{{ $row['class_name'] }}</td>
                        <td>{{ $row['student'] }}</td>
                        <td>{{ $row['status'] }}</td>
                        <td>{{ $row['notes'] }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">Belum ada detail absensi yang dapat ditampilkan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</section>
