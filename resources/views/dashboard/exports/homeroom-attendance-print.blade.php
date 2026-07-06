<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>{{ $pageTitle }} - {{ $schoolName }}</title>
    <style>
        body {
            color: #173d55;
            font-family: Arial, sans-serif;
            margin: 32px;
        }

        h1 {
            margin: 0 0 6px;
            font-size: 26px;
        }

        p {
            margin: 0 0 18px;
            color: #5c7183;
        }

        .summary {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 10px;
            margin: 18px 0;
        }

        .summary div {
            border: 1px solid #b9dff2;
            border-radius: 8px;
            padding: 10px;
        }

        .summary span {
            display: block;
            color: #5c7183;
            font-size: 12px;
        }

        .summary strong {
            display: block;
            margin-top: 4px;
            font-size: 22px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 18px;
        }

        th,
        td {
            border: 1px solid #b9dff2;
            padding: 8px;
            text-align: left;
        }

        th {
            background: #eef8fd;
        }

        @media print {
            button {
                display: none;
            }

            body {
                margin: 18px;
            }
        }
    </style>
</head>

<body>
    <button type="button" onclick="window.print()">Cetak / Simpan PDF</button>
    <h1>Rekap Absensi Kelas Perwalian</h1>
    <p>{{ $schoolName }} | {{ $activeAcademicYear }} | {{ ucfirst($activeSemester) }}</p>

    <section class="summary">
        <div><span>Total</span><strong>{{ $classAttendanceSummary['total'] }}</strong></div>
        <div><span>Hadir</span><strong>{{ $classAttendanceSummary['hadir'] }}</strong></div>
        <div><span>Izin</span><strong>{{ $classAttendanceSummary['izin'] }}</strong></div>
        <div><span>Sakit</span><strong>{{ $classAttendanceSummary['sakit'] }}</strong></div>
        <div><span>Alpha</span><strong>{{ $classAttendanceSummary['alpha'] }}</strong></div>
    </section>

    <h2>Rekap Per Kelas</h2>
    <table>
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
            @foreach ($classAttendanceRecapRows as $row)
            <tr>
                <td>{{ $row['class_name'] }}</td>
                <td>{{ $row['dates_count'] }}</td>
                <td>{{ $row['hadir'] }}</td>
                <td>{{ $row['izin'] }}</td>
                <td>{{ $row['sakit'] }}</td>
                <td>{{ $row['alpha'] }}</td>
                <td>{{ $row['present_rate'] }}%</td>
                <td>{{ $row['latest_date'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <h2>Detail Catatan Siswa</h2>
    <table>
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
            @foreach ($classAttendanceDetailRows as $row)
            <tr>
                <td>{{ $row['date'] }}</td>
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
