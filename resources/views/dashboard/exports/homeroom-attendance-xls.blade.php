<table>
    <thead>
        <tr>
            <th colspan="6">Rekap Absensi Kelas Perwalian - {{ $schoolName }}</th>
        </tr>
        <tr>
            <th>Konteks</th>
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
            <td>Absensi Kelas Perwalian</td>
            <td>{{ $row['date'] }}</td>
            <td>{{ $row['class_name'] }}</td>
            <td>{{ $row['student'] }}</td>
            <td>{{ $row['status'] }}</td>
            <td>{{ $row['notes'] }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
