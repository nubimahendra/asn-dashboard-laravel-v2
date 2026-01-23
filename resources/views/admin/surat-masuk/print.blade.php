<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Surat Masuk</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            margin: 0;
            padding: 20px;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
            font-size: 18px;
            text-transform: uppercase;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
            vertical-align: top;
        }

        th {
            background-color: #f0f0f0;
            font-weight: bold;
            text-align: center;
        }

        .text-center {
            text-align: center;
        }

        @media print {
            @page {
                size: 215mm 330mm;
                /* F4 size */
                margin: 10mm;
            }

            body {
                padding: 0;
            }
        }
    </style>
</head>

<body onload="window.print()">

    <h2>
        Laporan Surat Masuk<br>
        Bulan {{ $monthName }} {{ $year }}
    </h2>

    <table>
        <thead>
            <tr>
                <th style="width: 5%">No</th>
                <th style="width: 10%">No. Agenda</th>
                <th style="width: 15%">No. Surat</th>
                <th style="width: 10%">Tanggal</th>
                <th style="width: 20%">Pengirim</th>
                <th style="width: 20%">Perihal</th>
                <th style="width: 10%">Disposisi</th>
                <th style="width: 10%">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($suratMasuks as $index => $surat)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $surat->nomor_agenda }}</td>
                    <td>{{ $surat->nomor_surat }}</td>
                    <td class="text-center">{{ $surat->tanggal_terima ? $surat->tanggal_terima->format('d/m/Y') : '-' }}
                    </td>
                    <td>{{ $surat->pengirim }}</td>
                    <td>{{ $surat->perihal }}</td>
                    <td>{{ $surat->disposisi }}</td>
                    <td>{{ $surat->keterangan }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center">Tidak ada data surat masuk pada periode ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

</body>

</html>