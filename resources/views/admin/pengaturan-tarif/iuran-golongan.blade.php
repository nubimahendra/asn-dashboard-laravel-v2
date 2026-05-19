@extends('layouts.mari')

@section('content')
    <div class="container mx-auto px-10 py-8">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-800 dark:text-white">Pengaturan Tarif Iuran per Golongan</h1>
                <p class="text-gray-500 dark:text-gray-400 mt-1">
                    Atur besaran iuran KORPRI untuk masing-masing golongan.
                </p>
            </div>
        </div>

        <!-- Tarif Iuran (Editable) -->
        <div id="tabel-tarif" class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden mb-8">
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
                        @foreach($iuranRatesPaginated as $rate)
                            @php $key = $rate->golongan_key; @endphp
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
                    </tbody>
                    <tfoot class="bg-emerald-50 dark:bg-emerald-900/20">
                        <tr>
                            <td class="px-6 py-4 font-bold text-emerald-800 dark:text-emerald-300">TOTAL</td>
                            <td class="px-6 py-4 font-bold text-emerald-800 dark:text-emerald-300">
                                {{ number_format($globalTotals['total_pegawai']) }}
                            </td>
                            <td class="px-6 py-4"></td>
                            <td class="px-6 py-4 text-right font-bold text-emerald-800 dark:text-emerald-300 text-lg">Rp
                                {{ number_format($globalTotals['total_iuran'], 0, ',', '.') }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700">
                {{ $iuranRatesPaginated->fragment('tabel-tarif')->links() }}
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
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

                fetch('{{ route("mari.iuran-korpri.update") }}', {
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
