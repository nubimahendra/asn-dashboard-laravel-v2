@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-10 py-8">
        <!-- Print Warning / Header Container -->
        <div class="flex flex-col md:flex-row md:items-center justify-between mb-6 gap-4 print:hidden">
            <div>
                <a href="{{ route('iuran-kelas-jabatan.index', ['bulan' => $bulan, 'tahun' => $tahun]) }}" 
                   class="inline-flex items-center text-sm font-medium text-blue-600 dark:text-blue-400 hover:underline mb-2">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Kembali ke Daftar OPD
                </a>
                <h1 class="text-3xl font-bold text-gray-800 dark:text-white">Rincian Iuran KORPRI</h1>
                <p class="text-gray-500 dark:text-gray-400 mt-1">Detail iuran per pegawai berdasarkan kelas jabatan</p>
            </div>
            <div class="flex items-center gap-3">
                <button onclick="window.print()" 
                        class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-100 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150 shadow-sm"
                        title="Klik untuk mencetak atau simpan sebagai PDF">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                    </svg>
                    Simpan PDF / Cetak
                </button>
            </div>
        </div>

        <!-- Printable Invoice Section -->
        <div id="print-area" class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-100 dark:border-gray-700 overflow-hidden print:shadow-none print:border-none print:bg-transparent">
            
            <!-- Invoice Header -->
            <div class="p-6 md:p-8 border-b border-gray-100 dark:border-gray-700 print:pb-4 print:border-b-2 print:border-black">
                <div class="text-center mb-6">
                    <h2 class="text-2xl font-bold text-gray-800 dark:text-white uppercase print:text-black">TAGIHAN IURAN KORPRI</h2>
                    <p class="text-gray-600 dark:text-gray-400 font-medium print:text-gray-800">Bulan {{ date("F", mktime(0, 0, 0, $bulan, 1)) }} {{ $tahun }}</p>
                </div>
                
                <div class="flex flex-col sm:flex-row justify-between gap-4 mt-6">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-1 print:text-gray-600">Unit Kerja / OPD:</p>
                        <h3 class="text-lg font-bold text-gray-800 dark:text-white print:text-black">{{ $opd }}</h3>
                    </div>
                    <div class="sm:text-right">
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-1 print:text-gray-600">Tanggal Cetak:</p>
                        <p class="text-sm font-medium text-gray-800 dark:text-gray-200 print:text-black">{{ date('d F Y') }}</p>
                    </div>
                </div>
            </div>

            <!-- Invoice Table -->
            <div class="p-6 md:p-8 print:pt-4">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-600 dark:text-gray-300 print:text-black">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700/50 dark:text-gray-300 border-b border-gray-200 dark:border-gray-600 print:bg-transparent print:border-black">
                            <tr>
                                <th scope="col" class="px-4 py-3 w-16 text-center font-semibold print:px-2">No</th>
                                <th scope="col" class="px-4 py-3 font-semibold print:px-2">Kelas Jabatan</th>
                                <th scope="col" class="px-4 py-3 text-center font-semibold print:px-2">Jumlah Pegawai</th>
                                <th scope="col" class="px-4 py-3 text-right font-semibold print:px-2">Nominal / Orang</th>
                                <th scope="col" class="px-4 py-3 text-right font-semibold print:px-2">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700 print:divide-gray-300">
                            @php $no = 1; @endphp
                            @forelse ($breakdownKelas as $kelas => $data)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors print:hover:bg-transparent">
                                    <td class="px-4 py-3 text-center print:px-2">{{ $no++ }}</td>
                                    <td class="px-4 py-3 font-medium text-gray-900 dark:text-white print:text-black print:px-2">Kelas {{ $kelas }}</td>
                                    <td class="px-4 py-3 text-center print:px-2">
                                        <span class="bg-blue-100 text-blue-800 text-xs font-semibold px-2.5 py-1 rounded dark:bg-blue-900 dark:text-blue-300 border border-blue-200 dark:border-blue-800 print:border-none print:px-0 print:py-0 print:bg-transparent print:text-black">
                                            {{ number_format($data['jumlah_pegawai']) }} Orang
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-right print:px-2">Rp {{ number_format($data['nominal_per_orang'], 0, ',', '.') }}</td>
                                    <td class="px-4 py-3 text-right font-semibold text-gray-800 dark:text-gray-200 print:text-black print:px-2">Rp {{ number_format($data['subtotal'], 0, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                                        Tidak ada data pegawai yang wajib iuran di kelas jabatan manapun pada instansi ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot class="bg-gray-50 dark:bg-gray-800/80 border-t-2 border-gray-200 dark:border-gray-600 font-bold print:bg-transparent print:border-black">
                            <tr>
                                <td colspan="2" class="px-4 py-4 text-right text-gray-700 dark:text-gray-300 print:px-2 print:text-black uppercase">Grand Total</td>
                                <td class="px-4 py-4 text-center text-gray-900 dark:text-white print:px-2 print:text-black">{{ number_format($totalPegawai) }} Orang</td>
                                <td class="px-4 py-4"></td>
                                <td class="px-4 py-4 text-right text-green-700 dark:text-green-400 print:px-2 print:text-black text-lg">Rp {{ number_format($totalIuran, 0, ',', '.') }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <!-- Footer Signature Area (For Print Only) -->
                <div class="hidden print:block mt-16">
                    <div class="flex justify-end">
                        <div class="text-center w-64">
                            <p class="mb-20 text-sm">Blitar, {{ date('d F Y') }}<br>Bendahara KORPRI</p>
                            <p class="font-bold underline text-sm">.......................................</p>
                            <p class="text-xs mt-1">NIP. .......................................</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        @media print {
            body { 
                background-color: white !important; 
                margin: 0;
                padding: 0;
            }
            .container {
                max-width: 100% !important;
                padding: 0 !important;
                margin: 0 !important;
            }
            /* Menyesuaikan ukuran kertas agar invoice bagus / tidak terpotong */
            @page {
                size: A4 portrait;
                margin: 1.5cm;
            }
            /* Sembunyikan elemen sidebar/navbar utama app (sesuaikan class dengan layout) */
            nav, header, aside, .sidebar { 
                display: none !important; 
            }
            main {
                margin: 0 !important;
                padding: 0 !important;
            }
        }
    </style>
@endsection
