<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Snapshot Data Pegawai</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 1cm;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            color: #333;
        }
        h2 {
            text-align: center;
            margin-bottom: 5px;
            font-size: 16px;
        }
        h3 {
            text-align: center;
            margin-top: 0;
            margin-bottom: 20px;
            font-size: 12px;
            color: #666;
        }
        .text-right {
            text-align: right;
            margin-bottom: 10px;
            font-size: 10px;
            color: #555;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 5px 4px;
            word-wrap: break-word;
        }
        th {
            background-color: #f4f4f4;
            font-weight: bold;
            text-align: left;
            text-transform: uppercase;
            font-size: 9px;
            color: #555;
        }
        tr:nth-child(even) {
            background-color: #fafafa;
        }
        .text-center {
            text-align: center;
        }
    </style>
</head>
<body>

    <h2>Laporan Data Pegawai ASN</h2>
    @if(isset($filterMonth) && $filterMonth)
        <h3>Periode: {{ date('F Y', strtotime($filterMonth . '-01')) }} (Snapshot)</h3>
    @else
        <h3>Periode Terkini (Live Database)</h3>
    @endif

    <div class="text-right">
        Tanggal Cetak: {{ date('d F Y') }}
    </div>

    <table>
        <thead>
            <tr>
                <th width="3%" class="text-center">No</th>
                <th width="12%">NIP</th>
                <th width="12%">Nama</th>
                <th width="12%">TTL</th>
                <th width="5%">L/P</th>
                <th width="7%">Agama</th>
                <th width="7%">Sts.Kawin</th>
                <th width="5%">Gol.</th>
                <th width="12%">Jabatan</th>
                <th width="15%">Unit Kerja</th>
                <th width="10%">Pendidikan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pegawai as $index => $p)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $p->nip_baru }}</td>
                    <td>{{ $p->nama }}</td>
                    <td>
                        {{ $p->tempat_lahir }}<br>
                        {{ $p->tanggal_lahir ? date('d-m-Y', strtotime($p->tanggal_lahir)) : '-' }}
                    </td>
                    <td class="text-center">{{ $p->jenis_kelamin }}</td>
                    <td>{{ $p->agama }}</td>
                    <td>{{ $p->status_kawin }}</td>
                    <td>{{ $p->golongan_nama ?? ($p->golongan ? $p->golongan->nama : '-') }}</td>
                    <td>{{ $p->jabatan_nama ?? ($p->jabatanBaru ? $p->jabatanBaru->nama : '-') }}<br><small>{{ $p->jenis_jabatan_nama ?? $p->jenis_jabatan }}</small></td>
                    <td>{{ $p->unor_nama ?? ($p->unor ? $p->unor->nama : '-') }}</td>
                    <td>{{ $p->pendidikan_terakhir }}<br><small>{{ $p->tingkat_pendidikan }}</small></td>
                </tr>
            @empty
                <tr>
                    <td colspan="11" class="text-center" style="padding: 20px;">Tidak ada data pegawai.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>
