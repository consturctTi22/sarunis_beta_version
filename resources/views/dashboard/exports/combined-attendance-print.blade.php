<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="utf-8">
        <title>{{ $pageTitle }} - {{ $schoolName }}</title>
        <style>
            body { color: #173d55; font-family: Arial, sans-serif; margin: 32px; }
            h1 { margin: 0 0 6px; font-size: 26px; }
            h2 { margin: 26px 0 8px; font-size: 18px; }
            p { margin: 0 0 18px; color: #5c7183; }
            .summary { display: grid; grid-template-columns: repeat(5, 1fr); gap: 10px; margin: 14px 0 18px; }
            .summary div { border: 1px solid #b9dff2; border-radius: 8px; padding: 10px; }
            .summary span { display: block; color: #5c7183; font-size: 12px; }
            .summary strong { display: block; margin-top: 4px; font-size: 22px; }
            table { width: 100%; border-collapse: collapse; margin-top: 12px; }
            th, td { border: 1px solid #b9dff2; padding: 8px; text-align: left; }
            th { background: #eef8fd; }
            @media print { button { display: none; } body { margin: 18px; } }
        </style>
    </head>
    <body>
        <button type="button" onclick="window.print()">Cetak / Simpan PDF</button>
        <h1>Rekap Absensi {{ ucfirst($exportScope ?? 'gabungan') }}</h1>
        <p>{{ $schoolName }} | {{ $activeAcademicYear }} | {{ ucfirst($activeSemester) }}</p>

        @if (($exportScope ?? 'gabungan') !== 'kelas')
            <h2>Ringkasan Mapel</h2>
            <section class="summary">
                <div><span>Total</span><strong>{{ $attendanceSummary['total'] }}</strong></div>
                <div><span>Hadir</span><strong>{{ $attendanceSummary['hadir'] }}</strong></div>
                <div><span>Izin</span><strong>{{ $attendanceSummary['izin'] }}</strong></div>
                <div><span>Sakit</span><strong>{{ $attendanceSummary['sakit'] }}</strong></div>
                <div><span>Alpha</span><strong>{{ $attendanceSummary['alpha'] }}</strong></div>
            </section>
        @endif

        @if (($exportScope ?? 'gabungan') !== 'mapel')
            <h2>Ringkasan Kelas Perwalian</h2>
            <section class="summary">
                <div><span>Total</span><strong>{{ $classAttendanceSummary['total'] }}</strong></div>
                <div><span>Hadir</span><strong>{{ $classAttendanceSummary['hadir'] }}</strong></div>
                <div><span>Izin</span><strong>{{ $classAttendanceSummary['izin'] }}</strong></div>
                <div><span>Sakit</span><strong>{{ $classAttendanceSummary['sakit'] }}</strong></div>
                <div><span>Alpha</span><strong>{{ $classAttendanceSummary['alpha'] }}</strong></div>
            </section>
        @endif

        <h2>Detail Absensi</h2>
        <table>
            <thead>
                <tr>
                    <th>Konteks</th>
                    <th>Tanggal</th>
                    <th>Mapel</th>
                    <th>Kelas</th>
                    <th>Siswa</th>
                    <th>Status</th>
                    <th>Catatan</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($combinedAttendanceDetailRows as $row)
                    <tr>
                        <td>{{ $row['context'] }}</td>
                        <td>{{ $row['date'] }}</td>
                        <td>{{ $row['subject'] }}</td>
                        <td>{{ $row['class_name'] }}</td>
                        <td>{{ $row['student'] }}</td>
                        <td>{{ $row['status'] }}</td>
                        <td>{{ $row['notes'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </body>
</html>
