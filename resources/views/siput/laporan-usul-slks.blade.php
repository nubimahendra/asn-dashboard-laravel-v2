<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Usul SLKS</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            margin: 0;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header h2 {
            margin: 5px 0;
            font-size: 16px;
            text-transform: uppercase;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            border: 1px solid #000;
            padding: 6px;
            text-align: left;
            vertical-align: top;
        }

        th {
            background-color: #f2f2f2;
            text-align: center;
            font-weight: bold;
        }

        /* Merged headers */
        .sub-th {
            font-size: 10px;
        }

        /* Kolom ukuran */
        .col-no { width: 3%; text-align: center; }
        .col-nama { width: 15%; }
        .col-nip { width: 12%; }
        .col-pangkat { width: 10%; }
        .col-jabatan { width: 15%; }
        .col-slks-ada { width: 8%; text-align: center; }
        .col-no-slks { width: 12%; }
        .col-tgl-slks { width: 8%; text-align: center; }
        .col-usul { width: 8%; text-align: center; }
        .col-mk { width: 9%; text-align: center; }

        @page {
            size: 330mm 215mm landscape; /* F4 landscape */
            margin: 15mm;
        }

        @media print {
            .no-print {
                display: none;
            }
            body {
                padding: 0;
            }
        }
        
        .print-btn {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
            margin-right: 10px;
        }
        
        .close-btn {
            background-color: #f44336;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
        }
    </style>
</head>
<body onload="window.print()">
    <div class="header">
        <h2>DAFTAR USULAN TANDA KEHORMATAN SATYALANCANA KARYA SATYA</h2>
        <h2>Pemerintah Kabupaten Blitar</h2>
    </div>

    <table>
        <thead>
            <tr>
                <th rowspan="2" class="col-no">NO USUL</th>
                <th rowspan="2" class="col-nama">NAMA</th>
                <th rowspan="2" class="col-nip">NIP</th>
                <th rowspan="2" class="col-pangkat">PANGKAT</th>
                <th rowspan="2" class="col-jabatan">JABATAN</th>
                <th colspan="3">SLKS YANG SUDAH DIMILIKI</th>
                <th rowspan="2" class="col-usul">USUL SLKS</th>
                <th rowspan="2" class="col-mk">MASA KERJA</th>
            </tr>
            <tr>
                <th class="sub-th col-slks-ada">SLKS</th>
                <th class="sub-th col-no-slks">NOMOR SLKS</th>
                <th class="sub-th col-tgl-slks">TANGGAL SLKS</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $index => $item)
                <tr>
                    <td style="text-align: center;">{{ $index + 1 }}</td>
                    <td>{{ $item->nama }}</td>
                    <td>{{ $item->nip }}</td>
                    <td>{{ $item->pangkat }}</td>
                    <td>{{ $item->jabatan }}</td>
                    <td style="text-align: center;">{{ $item->slks_ada ?? '-' }}</td>
                    <td>{{ $item->no_slks ?? '-' }}</td>
                    <td style="text-align: center;">{{ $item->tgl_slks ? $item->tgl_slks->format('d/m/Y') : '-' }}</td>
                    <td style="text-align: center;">{{ $item->usul_slks ?? '-' }}</td>
                    <td style="text-align: center;">{{ $item->masa_kerja_tahun ? $item->masa_kerja_tahun . ' Thn ' . $item->masa_kerja_bulan . ' Bln' : '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" style="text-align: center; padding: 20px;">Belum ada data usulan SLKS.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="no-print" style="margin-top: 30px; text-align: center;">
        <button class="print-btn" onclick="window.print()">Cetak PDF (Print)</button>
        <button class="close-btn" onclick="window.close()">Tutup</button>
    </div>
</body>
</html>
