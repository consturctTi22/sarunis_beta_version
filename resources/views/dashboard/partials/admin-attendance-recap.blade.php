@php
    $teacherSummaries = collect($tableRows)
        ->groupBy('homeroom_teacher')
        ->map(function ($rows, $teacher): array {
            return [
                'teacher' => $teacher,
                'class_count' => $rows->count(),
                'students_count' => $rows->sum('students_count'),
                'present_count' => $rows->sum('present_count'),
            ];
        })
        ->values();
@endphp

<section class="portal-panel portal-table-card" id="rekap-kehadiran" data-dashboard-section data-section-label="Rekap Kehadiran">
    <div class="portal-section-heading">
        <div>
            <h2>Rekap Kehadiran Kelas</h2>
            <p>{{ $latestAttendanceDate ? 'Data terakhir '.$latestAttendanceDate : 'Belum ada data absensi kelas.' }}</p>
        </div>

        <div class="portal-segmented-tabs" role="tablist" aria-label="Tampilan rekap admin">
            <button class="is-active" type="button" role="tab" data-admin-panel-tab="students">Siswa</button>
            <button type="button" role="tab" data-admin-panel-tab="teachers">Guru</button>
        </div>
    </div>

    <div data-admin-panel="students">
        <div class="table-responsive">
            <table class="table portal-table mb-0">
                <thead>
                    <tr>
                        <th>Kelas</th>
                        <th>Wali Kelas</th>
                        <th>Jumlah Siswa</th>
                        <th>Hadir</th>
                        <th>Tidak Hadir</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($tableRows as $row)
                        <tr data-search-item>
                            <td>{{ $row['class_name'] }}</td>
                            <td>{{ $row['homeroom_teacher'] }}</td>
                            <td>{{ $row['students_count'] }}</td>
                            <td>{{ $row['present_count'] }}</td>
                            <td>{{ $row['absent_count'] }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">Belum ada data kelas untuk ditampilkan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="portal-admin-summary-grid d-none" data-admin-panel="teachers">
        @forelse ($teacherSummaries as $summaryCard)
            <article class="portal-panel portal-admin-summary-card" data-search-item>
                <h3>{{ $summaryCard['teacher'] }}</h3>
                <p>{{ $summaryCard['class_count'] }} kelas aktif</p>
                <div class="portal-admin-summary-card__stats">
                    <span>{{ $summaryCard['students_count'] }} siswa</span>
                    <span>{{ $summaryCard['present_count'] }} hadir</span>
                </div>
            </article>
        @empty
            <article class="portal-panel portal-placeholder-card">
                <h2>Data Guru</h2>
                <p>Belum ada guru yang terhubung ke rekap saat ini.</p>
            </article>
        @endforelse
    </div>
</section>
