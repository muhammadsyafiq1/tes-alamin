
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Covernote</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .header {
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .footer {
            text-align: center;
            font-size: 10px;
            margin-top: 20px;
        }
    </style>
</head>
<body>

    <div class="header">Covernote Asuransi</div>

    <table>
        <tr>
            <th>Nama Peserta</th>
            <td>{{ $peserta->nama }}</td>
        </tr>
        <tr>
            <th>Tempat, Tanggal Lahir</th>
            <td>{{ $peserta->tempat_lahir }}, {{ $peserta->tanggal_lahir }}</td>
        </tr>
        <tr>
            <th>Alamat</th>
            <td>{{ $peserta->alamat }}</td>
        </tr>
        <tr>
            <th>Masa Asuransi</th>
            <td>{{ $peserta->tanggal_mulai_asuransi }} s/d {{ $peserta->tanggal_selesai_asuransi }}</td>
        </tr>
        <tr>
            <th>Status Peserta</th>
            <td>{{ ucfirst($peserta->status_peserta) }}</td>
        </tr>
    </table>

</body>
</html>
