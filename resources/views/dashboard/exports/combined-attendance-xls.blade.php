<table>
    <thead>
        <tr>
            <th colspan="7">Rekap Absensi {{ ucfirst($exportScope ?? 'gabungan') }} - {{ $schoolName }}</th>
        </tr>
        <tr>
            <th colspan="7">{{ $activeAcademicYear }} | {{ ucfirst($activeSemester) }}</th>
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
