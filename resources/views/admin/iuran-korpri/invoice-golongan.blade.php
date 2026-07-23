<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice KORPRI (Golongan) - {{ $invoiceTitle }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @media print {
            body {
                background-color: white !important;
                color: black !important;
            }
            .no-print {
                display: none !important;
            }
            @page {
                size: portrait;
                margin: 1.5cm;
            }
            table {
                page-break-inside: auto;
            }
            tr {
                page-break-inside: avoid;
                page-break-after: auto;
            }
            thead {
                display: table-header-group;
            }
            tfoot {
                display: table-footer-group;
            }
            .print-border {
                border: 1px solid #000 !important;
            }
            .print-border-b {
                border-bottom: 1px solid #000 !important;
            }
            .print-border-r {
                border-right: 1px solid #000 !important;
            }
            .print-text-black {
                color: black !important;
            }
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen font-sans text-gray-900 py-8 print:py-0">
    @php
        $bulanIndo = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
    @endphp
    <div class="max-w-5xl mx-auto bg-white p-8 md:p-12 shadow-lg rounded-xl print:shadow-none print:rounded-none print:p-0">
        
        <!-- Header Actions -->
        <div class="flex justify-end mb-6 no-print">
            <button onclick="window.print()" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 transition-colors font-medium text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                Cetak Invoice
            </button>
        </div>

        <!-- Invoice Header -->
        <div class="text-center mb-8 border-b-2 border-gray-800 pb-4 print:border-b-[3px] print:border-black">
            @if(!empty($invoiceSettings['logo']))
                <img src="{{ Storage::url($invoiceSettings['logo']) }}" alt="Logo KORPRI" class="mx-auto h-24 mb-4 object-contain">
            @endif
            <h1 class="text-2xl font-bold uppercase tracking-wider print-text-black">INVOICE IURAN KORPRI</h1>
            <h2 class="text-xl font-semibold mt-1 print-text-black">Dewan Pengurus KORPRI Kab. Blitar</h2>
        </div>

        <!-- Invoice Details -->
        <div class="grid grid-cols-2 gap-4 mb-8 text-sm md:text-base print:text-sm print-text-black">
            <div>
                <table class="w-full">
                    <tr>
                        <td class="w-24 font-semibold text-gray-600 print-text-black align-top">PD / Unit</td>
                        <td class="w-4 align-top">:</td>
                        <td class="font-bold print-text-black">{{ $invoiceTitle }}</td>
                    </tr>
                    <tr>
                        <td class="font-semibold text-gray-600 print-text-black align-top">Bulan</td>
                        <td class="align-top">:</td>
                        <td class="print-text-black">{{ $bulanIndo[(int)$bulan] }} {{ $tahun }}</td>
                    </tr>
                </table>
            </div>
            <div>
                <table class="w-full">
                    <tr>
                        <td class="w-32 font-semibold text-gray-600 print-text-black align-top">Total Pegawai</td>
                        <td class="w-4 align-top">:</td>
                        <td class="print-text-black">{{ number_format($totalPegawai) }} Orang</td>
                    </tr>
                    <tr>
                        <td class="font-semibold text-gray-600 print-text-black align-top">Tanggal Cetak</td>
                        <td class="align-top">:</td>
                        <td class="print-text-black">{{ \Carbon\Carbon::now()->locale('id')->translatedFormat('d F Y H:i') }}</td>
                    </tr>
                </table>
            </div>
        </div>

        @if(empty($invoiceData))
            <div class="text-center py-10 text-gray-500 border rounded-lg print:border-black">
                Tidak ada data pegawai yang wajib membayar iuran untuk filter ini.
            </div>
        @else
            <!-- Data Tables -->
            @foreach($invoiceData as $opdName => $rows)
                @if($invoiceTitle === 'Seluruh PD' || in_array($invoiceTitle, ['Dinas Pendidikan', 'Dinas Kesehatan']))
                    <div class="mt-8 mb-3 bg-gray-100 p-2 font-bold text-gray-800 border-l-4 border-blue-600 print:bg-gray-200 print:border-black print-text-black">
                        {{ $opdName }}
                    </div>
                @endif
                
                <table class="w-full text-sm text-left border-collapse print-border print:text-xs mb-8">
                    <thead class="bg-gray-100 print:bg-gray-200 text-gray-700 print-text-black print-border">
                        <tr>
                            <th class="py-2 px-3 border print-border w-12 text-center">No</th>
                            <th class="py-2 px-3 border print-border">Golongan / Eselon</th>
                            <th class="py-2 px-3 border print-border text-center w-24">Dasar</th>
                            <th class="py-2 px-3 border print-border text-center w-28">Jumlah</th>
                            <th class="py-2 px-3 border print-border text-right w-36">Besaran/Orang (Rp)</th>
                            <th class="py-2 px-3 border print-border text-right w-36">Jumlah Iuran (Rp)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php 
                            $opdTotal = 0; 
                            $no = 1;
                        @endphp
                        @foreach($rows as $r)
                            @php $opdTotal += $r['subtotal']; @endphp
                            <tr class="border-b print-border-b print-text-black">
                                <td class="py-2 px-3 border-x print-border-r text-center align-top">{{ $no++ }}</td>
                                <td class="py-2 px-3 border-x print-border-r align-top font-semibold">{{ $r['key'] }}</td>
                                <td class="py-2 px-3 border-x print-border-r text-center align-top">{{ $r['dasar'] }}</td>
                                <td class="py-2 px-3 border-x print-border-r text-center align-top">{{ $r['jumlah_orang'] }} Orang</td>
                                <td class="py-2 px-3 border-x print-border-r text-right align-top">{{ number_format($r['besaran'], 0, ',', '.') }}</td>
                                <td class="py-2 px-3 border-x print-border-r text-right align-top font-medium">{{ number_format($r['subtotal'], 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50 print:bg-gray-100 print-border print-text-black">
                        <tr>
                            <td colspan="5" class="py-3 px-3 border print-border text-right font-bold uppercase text-gray-700 print-text-black">
                                Sub Total @if($invoiceTitle === 'Seluruh PD' || in_array($invoiceTitle, ['Dinas Pendidikan', 'Dinas Kesehatan'])) {{ $opdName }} @endif
                            </td>
                            <td class="py-3 px-3 border print-border text-right font-bold text-gray-900 print-text-black">
                                {{ number_format($opdTotal, 0, ',', '.') }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            @endforeach

            <!-- Grand Total -->
            @if($invoiceTitle === 'Seluruh PD' || in_array($invoiceTitle, ['Dinas Pendidikan', 'Dinas Kesehatan']))
            <div class="flex justify-end mt-4 print:mt-8 mb-8">
                <table class="w-72 print-border print-text-black">
                    <tr class="bg-blue-600 text-white print:bg-gray-300 print:text-black">
                        <td class="py-3 px-4 font-bold text-lg border print-border">JUMLAH TOTAL</td>
                        <td class="py-3 px-4 text-right font-bold text-lg border print-border">Rp {{ number_format($totalIuran, 0, ',', '.') }}</td>
                    </tr>
                </table>
            </div>
            @else
            <div class="flex justify-end mt-4 print:mt-8 mb-8">
                <table class="w-72 print-border print-text-black">
                    <tr class="bg-blue-600 text-white print:bg-gray-300 print:text-black">
                        <td class="py-3 px-4 font-bold text-lg border print-border">JUMLAH TOTAL</td>
                        <td class="py-3 px-4 text-right font-bold text-lg border print-border">Rp {{ number_format($totalIuran, 0, ',', '.') }}</td>
                    </tr>
                </table>
            </div>
            @endif
        @endif

        <!-- Footer Notes & Signatures -->
        <div class="mt-12 pt-8 border-t border-gray-300 print:border-black print-text-black">
            @if(!empty($invoiceSettings['bank_nama']) && !empty($invoiceSettings['bank_rekening']))
                @php
                    $nextMonth = $bulan == 12 ? 1 : $bulan + 1;
                    $nextYear = $bulan == 12 ? $tahun + 1 : $tahun;
                    $namaBulan = $bulanIndo[(int)$nextMonth];
                @endphp
                <p class="text-sm font-medium mb-4 italic">
                    Harap setor ke Rek KORPRI Bank {{ $invoiceSettings['bank_nama'] }} No. Rek {{ $invoiceSettings['bank_rekening'] }} 
                    an. {{ $invoiceSettings['bank_atas_nama'] }} 
                    sebelum tanggal {{ $invoiceSettings['batas_setor'] }} {{ $namaBulan }} {{ $nextYear }}.
                </p>
            @endif
            
            <p class="text-sm md:text-base font-bold text-center mt-12 mb-4">
                "Bersama KORPRI mari wujudkan Blitar Berdaya dan Berjaya"
            </p>
        </div>
    </div>
</body>
</html>
