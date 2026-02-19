@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-10 py-8">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-800 dark:text-white">Laporan Iuran KORPRI</h1>
                <p class="text-gray-500 dark:text-gray-400 mt-1">
                    @if($filterOpd)
                        <span
                            class="bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 text-xs font-semibold px-2.5 py-0.5 rounded mr-2">FILTERED</span>
                        {{ $filterOpd }}
                    @else
                        Pemerintah Kabupaten Blitar — Semua OPD
                    @endif
                </p>
            </div>
            <div class="flex items-center gap-3">
                @if($filterOpd)
                    <a href="{{ route('iuran-korpri.index') }}"
                        class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-200 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 transition">Reset</a>
                @endif
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 border-l-4 border-blue-500">
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Total Pegawai</p>
                <h2 class="text-3xl font-bold text-gray-800 dark:text-white">
                    {{ number_format($globalTotals['total_pegawai']) }}</h2>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 border-l-4 border-green-500">
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Ber-Golongan</p>
                <h2 class="text-3xl font-bold text-gray-800 dark:text-white">
                    {{ number_format($globalTotals['total_ber_golongan']) }}</h2>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 border-l-4 border-orange-500">
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Non-Golongan (PPPK PW)</p>
                <h2 class="text-3xl font-bold text-gray-800 dark:text-white">
                    {{ number_format($globalTotals['total_non_golongan']) }}</h2>
            </div>
            <div
                class="bg-gradient-to-r from-emerald-500 to-teal-500 dark:from-emerald-700 dark:to-teal-700 rounded-xl shadow-lg p-6">
                <p class="text-sm text-emerald-100 mb-1">Total Iuran KORPRI</p>
                <h2 class="text-3xl font-bold text-white">Rp {{ number_format($globalTotals['total_iuran'], 0, ',', '.') }}
                </h2>
            </div>
        </div>

        <!-- Breakdown per OPD Table -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden mb-8">
            <div
                class="p-6 border-b border-gray-100 dark:border-gray-700 flex flex-col md:flex-row justify-between items-center gap-4">
                <h3 class="text-lg font-bold text-gray-700 dark:text-gray-200">Rincian Iuran per OPD</h3>
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
                                class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider sticky left-0 bg-gray-50 dark:bg-gray-900/50 z-10">
                                No</th>
                            <th
                                class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider sticky left-10 bg-gray-50 dark:bg-gray-900/50 z-10 min-w-[200px]">
                                OPD</th>
                            @foreach($iuranRates as $key => $rate)
                                <th
                                    class="px-4 py-3 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider whitespace-nowrap">
                                    Gol {{ $key }}</th>
                            @endforeach
                            <th
                                class="px-4 py-3 text-center text-xs font-semibold text-orange-500 dark:text-orange-400 uppercase tracking-wider whitespace-nowrap">
                                Non-Gol</th>
                            <th
                                class="px-4 py-3 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Total</th>
                            <th
                                class="px-4 py-3 text-right text-xs font-semibold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider">
                                Total Iuran</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700" id="opd-breakdown-body">
                        @php $no = ($opdBreakdown->currentPage() - 1) * 10 + 1; @endphp
                        @forelse($opdBreakdown as $opdName => $opd)
                            <tr class="opd-row hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors"
                                data-name="{{ strtolower($opdName) }}">
                                <td
                                    class="px-4 py-3 text-gray-500 dark:text-gray-400 sticky left-0 bg-white dark:bg-gray-800 z-10">
                                    {{ $no++ }}</td>
                                <td
                                    class="px-4 py-3 font-medium text-gray-700 dark:text-gray-200 sticky left-10 bg-white dark:bg-gray-800 z-10">
                                    {{ $opdName }}
                                </td>
                                @foreach($iuranRates as $key => $rate)
                                    <td class="px-4 py-3 text-center text-gray-600 dark:text-gray-300">
                                        {{ $opd['per_golongan'][$key] ?? 0 }}
                                    </td>
                                @endforeach
                                <td class="px-4 py-3 text-center text-orange-600 dark:text-orange-400 font-medium">
                                    {{ $opd['total_non_golongan'] }}</td>
                                <td class="px-4 py-3 text-center font-semibold text-gray-700 dark:text-gray-200">
                                    {{ number_format($opd['total_pegawai']) }}</td>
                                <td class="px-4 py-3 text-right font-semibold text-emerald-700 dark:text-emerald-300">Rp
                                    {{ number_format($opd['total_iuran'], 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ count($iuranRates) + 4 }}"
                                    class="px-6 py-12 text-center text-gray-400 dark:text-gray-500">
                                    Tidak ada data pegawai ditemukan
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if(count($opdBreakdown) > 0)
                        <tfoot class="bg-emerald-50 dark:bg-emerald-900/20">
                            <tr>
                                <td class="px-4 py-4 sticky left-0 bg-emerald-50 dark:bg-emerald-900/20 z-10"></td>
                                <td
                                    class="px-4 py-4 font-bold text-emerald-800 dark:text-emerald-300 sticky left-10 bg-emerald-50 dark:bg-emerald-900/20 z-10">
                                    GRAND TOTAL</td>
                                @foreach($iuranRates as $key => $rate)
                                    <td class="px-4 py-4 text-center font-bold text-emerald-800 dark:text-emerald-300">
                                        {{ $globalTotals['per_golongan'][$key]['count'] ?? 0 }}</td>
                                @endforeach
                                <td class="px-4 py-4 text-center font-bold text-orange-700 dark:text-orange-300">
                                    {{ $globalTotals['total_non_golongan'] }}</td>
                                <td class="px-4 py-4 text-center font-bold text-emerald-800 dark:text-emerald-300">
                                    {{ number_format($globalTotals['total_pegawai']) }}</td>
                                <td class="px-4 py-4 text-right font-bold text-emerald-800 dark:text-emerald-300 text-lg">Rp
                                    {{ number_format($globalTotals['total_iuran'], 0, ',', '.') }}</td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>
        <div class="mb-8">
            {{ $opdBreakdown->links() }}
        </div>

        <!-- Tarif Iuran (Editable) - Moved to Bottom -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden mb-8">
            <div class="p-6 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                <h3 class="text-lg font-bold text-gray-700 dark:text-gray-200">Tarif Iuran per Golongan</h3>
                <button id="btn-save-tarif"
                    class="hidden inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-lg shadow-sm transition-colors focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Simpan Perubahan
                </button>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-900/50">
                        <tr>
                            <th
                                class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Golongan</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Jumlah Pegawai</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Besaran Iuran (Rp)</th>
                            <th
                                class="px-6 py-3 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @foreach($iuranRates as $key => $rate)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                <td class="px-6 py-4 font-medium text-gray-700 dark:text-gray-200">{{ $rate->label }}</td>
                                <td class="px-6 py-4 text-gray-600 dark:text-gray-300">
                                    {{ number_format($globalTotals['per_golongan'][$key]['count'] ?? 0) }}
                                </td>
                                <td class="px-6 py-4">
                                    <input type="number" data-rate-id="{{ $rate->id }}" data-original="{{ $rate->besaran }}"
                                        value="{{ $rate->besaran }}"
                                        class="tarif-input w-32 px-3 py-1.5 text-sm border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-colors"
                                        min="0" step="1000">
                                </td>
                                <td class="px-6 py-4 text-right font-semibold text-gray-700 dark:text-gray-200">
                                    Rp {{ number_format(($globalTotals['per_golongan'][$key]['subtotal'] ?? 0), 0, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach
                        <!-- Non-Golongan Row -->
                        <tr
                            class="bg-orange-50/50 dark:bg-orange-900/10 hover:bg-orange-50 dark:hover:bg-orange-900/20 transition-colors">
                            <td class="px-6 py-4 font-medium text-orange-700 dark:text-orange-300">Non-Golongan (PPPK PW)
                            </td>
                            <td class="px-6 py-4 text-orange-600 dark:text-orange-300">
                                {{ number_format($globalTotals['total_non_golongan']) }}</td>
                            <td class="px-6 py-4 text-gray-400 dark:text-gray-500 italic">—</td>
                            <td class="px-6 py-4 text-right text-gray-400 dark:text-gray-500 italic">—</td>
                        </tr>
                    </tbody>
                    <tfoot class="bg-emerald-50 dark:bg-emerald-900/20">
                        <tr>
                            <td class="px-6 py-4 font-bold text-emerald-800 dark:text-emerald-300">TOTAL</td>
                            <td class="px-6 py-4 font-bold text-emerald-800 dark:text-emerald-300">
                                {{ number_format($globalTotals['total_pegawai']) }}</td>
                            <td class="px-6 py-4"></td>
                            <td class="px-6 py-4 text-right font-bold text-emerald-800 dark:text-emerald-300 text-lg">Rp
                                {{ number_format($globalTotals['total_iuran'], 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>


    </div>
@endsection

@section('scripts')
    <script>
    // Filter OPD search (Breakdown Table)
    const breakdownSearch = document.getElementById('opd-breakdown-search');
    if (breakdownSearch) {
        breakdownSearch.addEventListener('input', function(e) {
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

        // Tarif edit detection
        const tarifInputs = document.querySelectorAll('.tarif-input');
        const btnSave = document.getElementById('btn-save-tarif');

        tarifInputs.forEach(input => {
            input.addEventListener('input', function () {
                let hasChanges = false;
                tarifInputs.forEach(inp => {
                    if (inp.value !== inp.dataset.original) hasChanges = true;
                });
                btnSave.classList.toggle('hidden', !hasChanges);
            });
        });

        // Save tarif
        if (btnSave) {
            btnSave.addEventListener('click', function () {
                const rates = [];
                tarifInputs.forEach(input => {
                    rates.push({
                        id: input.dataset.rateId,
                        besaran: parseInt(input.value) || 0
                    });
                });

                btnSave.disabled = true;
                btnSave.innerHTML = '<svg class="animate-spin w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Menyimpan...';

                fetch('{{ route("iuran-korpri.update") }}', {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ rates })
                })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            // Reload page to recalculate totals
                            window.location.reload();
                        } else {
                            alert('Gagal menyimpan: ' + data.message);
                            btnSave.disabled = false;
                            btnSave.innerHTML = '<svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>Simpan Perubahan';
                        }
                    })
                    .catch(err => {
                        alert('Error: ' + err.message);
                        btnSave.disabled = false;
                        btnSave.innerHTML = '<svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>Simpan Perubahan';
                    });
            });
        }
    </script>
@endsection