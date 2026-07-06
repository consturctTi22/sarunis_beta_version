<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="utf-8">
        <title>{{ $title }}</title>
        <style>
            body { color: #173d55; font-family: Arial, sans-serif; margin: 32px; }
            h1 { margin: 0 0 6px; font-size: 26px; }
            p { margin: 0 0 18px; color: #5c7183; }
            table { width: 100%; border-collapse: collapse; margin-top: 18px; }
            th, td { border: 1px solid #b9dff2; padding: 8px; text-align: left; vertical-align: top; }
            th { background: #eef8fd; }
            @media print { button { display: none; } body { margin: 18px; } }
        </style>
    </head>
    <body>
        <button type="button" onclick="window.print()">Cetak / Simpan PDF</button>
        <h1>{{ $title }}</h1>
        <p>Total data: {{ count($rows) }}</p>

        <table>
            <thead>
                <tr>
                    @foreach ($headers as $header)
                        <th>{{ $header }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach ($rows as $row)
                    <tr>
                        @foreach ($row as $value)
                            <td>{{ $value }}</td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    </body>
</html>
