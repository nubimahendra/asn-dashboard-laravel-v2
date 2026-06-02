@extends('layouts.mari')

@section('content')
    <div class="container mx-auto px-10 py-8">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-800 dark:text-white">Laporan Iuran KORPRI per PD</h1>
                <p class="text-gray-500 dark:text-gray-400 mt-1">
                    @if($filterOpd)
                        <span
                            class="bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 text-xs font-semibold px-2.5 py-0.5 rounded mr-2">FILTERED</span>
                        {{ $filterOpd }}
                    @else
                        Dewan Pengurus KORPRI Kab. Blitar — Semua PD
                    @endif
                </p>
            </div>
            <div class="flex items-center gap-3">
                <form method="GET" action="{{ route('mari.iuran-korpri.index') }}" class="flex items-center gap-4 bg-white dark:bg-gray-800 px-4 py-2 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm mr-2" id="filterForm">
                    @if($filterOpd)
                        <input type="hidden" name="opd" value="{{ $filterOpd }}">
                    @endif
                    <input type="hidden" name="pns" value="0">
                    <input type="hidden" name="pppk" value="0">
                    
                    <select name="bulan" class="text-sm rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200" onchange="this.form.submit()">
                        @foreach(range(1,12) as $m)
                            <option value="{{ $m }}" {{ $bulan == $m ? 'selected' : '' }}>{{ date('F', mktime(0,0,0,$m,10)) }}</option>
                        @endforeach
                    </select>
                    <select name="tahun" class="text-sm rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200" onchange="this.form.submit()">
                        @foreach(range(date('Y')-2, date('Y')+1) as $y)
                            <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endforeach
                    </select>

                    <label class="inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="pns" value="1" class="sr-only peer" {{ $pns ? 'checked' : '' }} onchange="this.form.submit()">
                        <div class="relative w-9 h-5 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                        <span class="ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">PNS</span>
                    </label>
                    <label class="inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="pppk" value="1" class="sr-only peer" {{ $pppk ? 'checked' : '' }} onchange="this.form.submit()">
                        <div class="relative w-9 h-5 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-green-300 dark:peer-focus:ring-green-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all dark:border-gray-600 peer-checked:bg-green-600"></div>
                        <span class="ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">PPPK</span>
                    </label>
                </form>
                <button type="submit" form="filterForm" name="hitung_ulang" value="1" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-200 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 transition" title="Hitung Ulang">
                    🔄
                </button>
                <a href="{{ route('mari.iuran-korpri.invoice-golongan', ['bulan' => $bulan, 'tahun' => $tahun, 'pns' => $pns, 'pppk' => $pppk, 'opd' => $filterOpd]) }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150 shadow-sm" title="Cetak Invoice Golongan">
                    📋
                </a>
                <a href="{{ route('mari.iuran-korpri.invoice', ['bulan' => $bulan, 'tahun' => $tahun, 'pns' => $pns, 'pppk' => $pppk, 'opd' => $filterOpd]) }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150 shadow-sm" title="Cetak Invoice Pegawai">
                    🧾
                </a>
                <button type="button" onclick="simpanIuran()" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150 shadow-sm" title="Simpan Iuran Bulan Ini">
                    💾
                </button>
                @if($filterOpd)
                    <a href="{{ route('mari.iuran-korpri.index') }}"
                        class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-200 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 transition">Reset</a>
                @endif
            </div>
        </div>

        @if($isArsip)
        <div class="bg-blue-50 dark:bg-blue-900/30 border-l-4 border-blue-500 p-4 mb-6 rounded-r-lg">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-blue-700 dark:text-blue-300">
                        <strong>Data Arsip:</strong> Menampilkan rekapan yang tersimpan untuk {{ date('F', mktime(0,0,0,$bulan,10)) }} {{ $tahun }}.
                        <br>
                        Disimpan oleh: {{ $arsipCreator }} pada {{ \Carbon\Carbon::parse($arsipDate)->format('d M Y H:i') }}
                    </p>
                </div>
            </div>
        </div>
        @endif

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 border-l-4 border-blue-500">
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Total Pegawai</p>
                <h2 class="text-3xl font-bold text-gray-800 dark:text-white">
                    {{ number_format($globalTotals['total_pegawai']) }}
                </h2>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 border-l-4 border-green-500">
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Ber-Golongan / Ber-Iuran</p>
                <h2 class="text-3xl font-bold text-gray-800 dark:text-white">
                    {{ number_format($globalTotals['total_ber_golongan']) }}
                </h2>
            </div>
            <div
                class="bg-gradient-to-r from-emerald-500 to-teal-500 dark:from-emerald-700 dark:to-teal-700 rounded-xl shadow-lg p-6 md:col-span-2">
                <p class="text-sm text-emerald-100 mb-1">Total Iuran KORPRI</p>
                <h2 class="text-3xl font-bold text-white">Rp {{ number_format($globalTotals['total_iuran'], 0, ',', '.') }}
                </h2>
            </div>
        </div>

        <!-- Breakdown per OPD Table -->
        <div id="tabel-opd" class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden mb-8">
            <div
                class="p-6 border-b border-gray-100 dark:border-gray-700 flex flex-col md:flex-row justify-between items-center gap-4">
                <h3 class="text-lg font-bold text-gray-700 dark:text-gray-200">Rincian Iuran per PD</h3>
                <div class="relative w-full md:w-64">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"><svg
                            class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg></span>
                    <input type="text" id="opd-breakdown-search"
                        class="w-full pl-10 pr-4 py-2 text-sm text-gray-700 bg-gray-50 dark:bg-gray-700 dark:text-gray-200 border border-gray-200 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors"
                        placeholder="Cari Unit Kerja...">
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-900/50">
                        <tr>
                            <th
                                class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                No</th>
                            <th
                                class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider min-w-[200px]">
                                PD</th>
                            <th
                                class="px-4 py-3 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Total Pegawai</th>
                            <th
                                class="px-4 py-3 text-right text-xs font-semibold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider">
                                Total Iuran</th>
                            <th
                                class="px-4 py-3 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700" id="opd-breakdown-body">
                        @php $no = ($opdBreakdown->currentPage() - 1) * 10 + 1; @endphp
                        @forelse($opdBreakdown as $index => $opd)
                            <tr class="opd-row hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors"
                                data-name="{{ strtolower($opd['nama_opd']) }}">
                                <td
                                    class="px-4 py-3 text-gray-500 dark:text-gray-400">
                                    {{ $no++ }}
                                </td>
                                <td
                                    class="px-4 py-3 font-medium text-gray-700 dark:text-gray-200">
                                    {{ $opd['nama_opd'] }}
                                </td>
                                <td class="px-4 py-3 text-center font-semibold text-gray-700 dark:text-gray-200">
                                    {{ number_format($opd['total_pegawai']) }}
                                </td>
                                <td class="px-4 py-3 text-right font-semibold text-emerald-700 dark:text-emerald-300">Rp
                                    {{ number_format($opd['total_iuran'], 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('mari.iuran-korpri.invoice-golongan', ['opd' => $opd['nama_opd'], 'bulan' => $bulan, 'tahun' => $tahun, 'pns' => $pns, 'pppk' => $pppk]) }}" target="_blank" class="inline-flex items-center justify-center p-1.5 bg-blue-50 text-blue-600 hover:bg-blue-100 dark:bg-blue-900/30 dark:text-blue-400 dark:hover:bg-blue-900/50 rounded-lg transition-colors" title="Cetak Invoice Golongan">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                                        </a>
                                        <a href="{{ route('mari.iuran-korpri.invoice', ['opd' => $opd['nama_opd'], 'bulan' => $bulan, 'tahun' => $tahun, 'pns' => $pns, 'pppk' => $pppk]) }}" target="_blank" class="inline-flex items-center justify-center p-1.5 bg-indigo-50 text-indigo-600 hover:bg-indigo-100 dark:bg-indigo-900/30 dark:text-indigo-400 dark:hover:bg-indigo-900/50 rounded-lg transition-colors" title="Cetak Invoice Pegawai">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4"
                                    class="px-6 py-12 text-center text-gray-400 dark:text-gray-500">
                                    Tidak ada data pegawai ditemukan
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if(count($opdBreakdown) > 0)
                        <tfoot class="bg-emerald-50 dark:bg-emerald-900/20">
                            <tr>
                                <td class="px-4 py-4"></td>
                                <td
                                    class="px-4 py-4 font-bold text-emerald-800 dark:text-emerald-300">
                                    GRAND TOTAL</td>
                                <td class="px-4 py-4 text-center font-bold text-emerald-800 dark:text-emerald-300">
                                    {{ number_format($globalTotals['total_pegawai']) }}
                                </td>
                                <td class="px-4 py-4 text-right font-bold text-emerald-800 dark:text-emerald-300 text-lg">Rp
                                    {{ number_format($globalTotals['total_iuran'], 0, ',', '.') }}
                                </td>
                                <td></td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>
        <div class="mb-8">
            {{ $opdBreakdown->fragment('tabel-opd')->links() }}
        </div>




    </div>
@endsection

@section('scripts')
    <script>
        // Filter OPD search (Breakdown Table)
        const breakdownSearch = document.getElementById('opd-breakdown-search');
        if (breakdownSearch) {
            breakdownSearch.addEventListener('input', function (e) {
                const term = e.target.value.toLowerCase();
                document.querySelectorAll('.opd-row').forEach(row => {
                    const name = row.getAttribute('data-name');
                    if (name.includes(term)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });
        }


        function simpanIuran() {
            if(!confirm('Simpan/Update rekapan iuran untuk bulan dan tahun yang dipilih ke database?')) return;
            
            const bulan = document.querySelector('select[name="bulan"]').value;
            const tahun = document.querySelector('select[name="tahun"]').value;

            fetch('{{ route("mari.iuran-korpri.simpan") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ bulan, tahun })
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    alert(data.message);
                    window.location.reload();
                } else {
                    alert('Gagal menyimpan: ' + data.message);
                }
            })
            .catch(err => {
                alert('Error: ' + err.message);
            });
        }
    </script>
@endsection