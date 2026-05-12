@extends('layouts.mari')

@section('content')
<div class="container mx-auto px-10 py-8">
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 dark:text-white">Rincian Iuran Pegawai</h1>
            <p class="text-gray-500 dark:text-gray-400 mt-1">
                @if($filterOpd)
                    <span class="bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 text-xs font-semibold px-2.5 py-0.5 rounded mr-2">FILTERED</span>
                    {{ $filterOpd }}
                @else
                    Semua OPD
                @endif
            </p>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm mb-8 border border-gray-100 dark:border-gray-700">
        <form method="GET" action="{{ route('mari.rincian-iuran.index') }}" class="flex flex-wrap items-end gap-6">
            <div class="flex-1 min-w-[300px]">
                <label for="opd" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Pilih OPD</label>
                <select name="opd" id="opd" class="w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 focus:border-blue-500 focus:ring-blue-500" onchange="this.form.submit()">
                    <option value="">Semua OPD</option>
                    @foreach($listOpd as $opdName)
                        <option value="{{ $opdName }}" {{ $filterOpd == $opdName ? 'selected' : '' }}>{{ $opdName }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex gap-4">
                <label class="inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="pns" value="1" class="sr-only peer" {{ $pns ? 'checked' : '' }} onchange="this.form.submit()">
                    <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                    <span class="ms-3 text-sm font-medium text-gray-900 dark:text-gray-300">PNS</span>
                </label>
                <label class="inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="pppk" value="1" class="sr-only peer" {{ $pppk ? 'checked' : '' }} onchange="this.form.submit()">
                    <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-green-300 dark:peer-focus:ring-green-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-green-600"></div>
                    <span class="ms-3 text-sm font-medium text-gray-900 dark:text-gray-300">PPPK</span>
                </label>
            </div>
        </form>
    </div>

    <!-- Grand Total Card -->
    <div class="bg-gradient-to-r from-emerald-500 to-teal-600 rounded-xl shadow-lg p-8 mb-8 text-white flex flex-col md:flex-row justify-between items-center gap-4">
        <div>
            <h2 class="text-xl font-medium text-emerald-100 mb-1">Total Keseluruhan Iuran</h2>
            <div class="text-sm text-emerald-200">Berdasarkan filter yang dipilih</div>
        </div>
        <div class="text-right">
            <div class="text-4xl font-bold mb-2">Rp {{ number_format($grandTotal['iuran'], 0, ',', '.') }}</div>
            <div class="text-emerald-100">Dari {{ number_format($grandTotal['pegawai']) }} Pegawai</div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Tabel Eselon -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
            <div class="p-6 border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50">
                <h3 class="text-lg font-bold text-gray-800 dark:text-white">Pegawai Struktural (Eselon)</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="text-xs text-gray-500 dark:text-gray-400 uppercase bg-gray-50 dark:bg-gray-900/50">
                        <tr>
                            <th class="px-6 py-4">Eselon</th>
                            <th class="px-6 py-4 text-center">Jumlah</th>
                            <th class="px-6 py-4 text-right">Tarif</th>
                            <th class="px-6 py-4 text-right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @php $totalEselonPeg = 0; $totalEselonIuran = 0; @endphp
                        @foreach($eselonBreakdown as $row)
                            @php $totalEselonPeg += $row['count']; $totalEselonIuran += $row['subtotal']; @endphp
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                <td class="px-6 py-4 font-medium text-gray-900 dark:text-gray-200">{{ $row['label'] }}</td>
                                <td class="px-6 py-4 text-center">{{ number_format($row['count']) }}</td>
                                <td class="px-6 py-4 text-right whitespace-nowrap">Rp {{ number_format($row['tarif'], 0, ',', '.') }}</td>
                                <td class="px-6 py-4 text-right font-semibold text-emerald-600 dark:text-emerald-400 whitespace-nowrap">Rp {{ number_format($row['subtotal'], 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50 dark:bg-gray-900/50 font-bold text-gray-900 dark:text-white border-t-2 border-gray-200 dark:border-gray-600">
                        <tr>
                            <td class="px-6 py-4">TOTAL STRUKTURAL</td>
                            <td class="px-6 py-4 text-center">{{ number_format($totalEselonPeg) }}</td>
                            <td class="px-6 py-4 text-right">-</td>
                            <td class="px-6 py-4 text-right text-emerald-600 dark:text-emerald-400 whitespace-nowrap">Rp {{ number_format($totalEselonIuran, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Tabel Golongan -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
            <div class="p-6 border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50">
                <h3 class="text-lg font-bold text-gray-800 dark:text-white">Pegawai Non-Struktural (Golongan)</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="text-xs text-gray-500 dark:text-gray-400 uppercase bg-gray-50 dark:bg-gray-900/50">
                        <tr>
                            <th class="px-6 py-4">Golongan</th>
                            <th class="px-6 py-4 text-center">Jumlah</th>
                            <th class="px-6 py-4 text-right">Tarif</th>
                            <th class="px-6 py-4 text-right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @php $totalGolPeg = 0; $totalGolIuran = 0; @endphp
                        @foreach($golonganBreakdown as $row)
                            @if($row['count'] > 0)
                                @php $totalGolPeg += $row['count']; $totalGolIuran += $row['subtotal']; @endphp
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                    <td class="px-6 py-4 font-medium text-gray-900 dark:text-gray-200">Golongan {{ $row['label'] }}</td>
                                    <td class="px-6 py-4 text-center">{{ number_format($row['count']) }}</td>
                                    <td class="px-6 py-4 text-right whitespace-nowrap">Rp {{ number_format($row['tarif'], 0, ',', '.') }}</td>
                                    <td class="px-6 py-4 text-right font-semibold text-emerald-600 dark:text-emerald-400 whitespace-nowrap">Rp {{ number_format($row['subtotal'], 0, ',', '.') }}</td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50 dark:bg-gray-900/50 font-bold text-gray-900 dark:text-white border-t-2 border-gray-200 dark:border-gray-600">
                        <tr>
                            <td class="px-6 py-4">TOTAL NON-STRUKTURAL</td>
                            <td class="px-6 py-4 text-center">{{ number_format($totalGolPeg) }}</td>
                            <td class="px-6 py-4 text-right">-</td>
                            <td class="px-6 py-4 text-right text-emerald-600 dark:text-emerald-400 whitespace-nowrap">Rp {{ number_format($totalGolIuran, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
