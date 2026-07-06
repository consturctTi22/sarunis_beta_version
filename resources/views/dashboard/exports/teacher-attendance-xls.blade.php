<table>
    <thead>
        <tr>
            <th colspan="7">Rekap Absensi Mapel - {{ $schoolName }}</th>
        </tr>
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
        @foreach ($attendanceDetailRows as $row)
            <tr>
                <td>Absensi Mapel</td>
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
