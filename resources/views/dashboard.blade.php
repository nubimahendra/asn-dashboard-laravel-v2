@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-10 py-8">
        <!-- Header / Topbar -->
        <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
            <div class="flex items-center gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800 dark:text-white">Dashboard Statistik ASN</h1>
                    <div class="flex items-center gap-2 mt-1">
                        <p class="text-gray-500 dark:text-gray-400 flex items-center">
                            @if(isset($filterOpd) && $filterOpd)
                                <span
                                    class="bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 text-xs font-semibold px-2.5 py-0.5 rounded mr-2">FILTERED</span>
                                {{ $filterOpd }}
                            @else
                                Pemerintah Kabupaten Blitar
                            @endif
                        </p>
                        <span class="text-gray-300 dark:text-gray-600">|</span>
                        <span class="text-xs text-gray-400 dark:text-gray-500" title="Last Synced"><svg
                                class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                                </path>
                            </svg> Updated: {{ $lastSync ?? 'N/A' }}</span>
                    </div>
                </div>
            </div>
            <div class="flex items-center gap-4">
                <button id="theme-toggle"
                    class="p-2 rounded-lg bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 shadow-sm focus:outline-none focus:ring-2 focus:ring-yellow-500">
                    <svg id="theme-toggle-light-icon" class="hidden w-6 h-6" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z">
                        </path>
                    </svg>
                    <svg id="theme-toggle-dark-icon" class="hidden w-6 h-6" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z">
                        </path>
                    </svg>
                </button>
                @if(isset($filterOpd) && $filterOpd)
                    <a href="/"
                        class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-200 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">Reset</a>
                @endif
                <form action="{{ route('logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit"
                        class="p-2 rounded-lg bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 shadow-sm focus:outline-none focus:ring-2 focus:ring-red-500 transition-colors"
                        title="Logout">
                        <svg class="w-6 h-6 text-gray-600 dark:text-gray-300 hover:text-red-600 dark:hover:text-red-400 transition-colors"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                            </path>
                        </svg>
                    </button>
                </form>
            </div>
        </div>



        <!-- Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div
                class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 border-l-4 border-blue-500 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Total Pegawai</p>
                        <h2 class="text-3xl font-bold text-gray-800 dark:text-white">{{ number_format($totalPegawai) }}</h2>
                    </div>
                    <div class="p-3 bg-blue-50 dark:bg-blue-900/30 rounded-full"><svg class="w-6 h-6 text-blue-500"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                            </path>
                        </svg></div>
                </div>
            </div>
            <div
                class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 border-l-4 border-teal-500 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Laki-laki</p>
                        <h2 class="text-3xl font-bold text-gray-800 dark:text-white">{{ number_format($totalLaki) }}</h2>
                    </div>
                    <div class="p-3 bg-teal-50 dark:bg-teal-900/30 rounded-full"><svg class="w-6 h-6 text-teal-500"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg></div>
                </div>
            </div>
            <div
                class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 border-l-4 border-pink-500 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Perempuan</p>
                        <h2 class="text-3xl font-bold text-gray-800 dark:text-white">{{ number_format($totalPerempuan) }}
                        </h2>
                    </div>
                    <div class="p-3 bg-pink-50 dark:bg-pink-900/30 rounded-full"><svg class="w-6 h-6 text-pink-500"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg></div>
                </div>
            </div>
            <div
                class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 border-l-4 border-green-500 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">PNS</p>
                        <h2 class="text-3xl font-bold text-gray-800 dark:text-white">{{ number_format($totalPns) }}</h2>
                    </div>
                    <div class="p-3 bg-green-50 dark:bg-green-900/30 rounded-full"><span
                            class="text-green-600 font-bold text-lg">PNS</span></div>
                </div>
            </div>
            <div
                class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 border-l-4 border-purple-500 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">CPNS</p>
                        <h2 class="text-3xl font-bold text-gray-800 dark:text-white">{{ number_format($totalCpns) }}</h2>
                    </div>
                    <div class="p-3 bg-purple-50 dark:bg-purple-900/30 rounded-full"><span
                            class="text-purple-600 font-bold text-lg">CPNS</span></div>
                </div>
            </div>
            <div
                class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 border-l-4 border-orange-500 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">PPPK</p>
                        <h2 class="text-3xl font-bold text-gray-800 dark:text-white">{{ number_format($totalPppk) }}</h2>
                    </div>
                    <div class="p-3 bg-orange-50 dark:bg-orange-900/30 rounded-full"><span
                            class="text-orange-600 font-bold text-lg">PPPK</span></div>
                </div>
            </div>
        </div>

        <!-- Charts -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                <h3 class="text-lg font-bold text-gray-700 dark:text-gray-200 mb-4">Pegawai per Jenis Kelamin</h3>
                <div id="chart-jenikel"></div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                <h3 class="text-lg font-bold text-gray-700 dark:text-gray-200 mb-4">Jenis Pegawai</h3>
                <div id="chart-sts-peg"></div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                <h3 class="text-lg font-bold text-gray-700 dark:text-gray-200 mb-4">Pegawai per Pendidikan</h3>
                <div id="chart-pendidikan"></div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                <h3 class="text-lg font-bold text-gray-700 dark:text-gray-200 mb-4">Pegawai per Eselon</h3>
                <div id="chart-eselon"></div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                <h3 class="text-lg font-bold text-gray-700 dark:text-gray-200 mb-4">Pegawai per Golongan</h3>
                <div id="chart-golongan"></div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                <h3 class="text-lg font-bold text-gray-700 dark:text-gray-200 mb-4">Pegawai per Generasi</h3>
                <div id="chart-generasi"></div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 lg:col-span-2">
                <h3 class="text-lg font-bold text-gray-700 dark:text-gray-200 mb-4"> @if(isset($filterOpd) && $filterOpd)
                Statistik Unit Kerja Ini @else Top 10 Unit Kerja / OPD @endif </h3>
                <div id="chart-opd"></div>
            </div>
        </div>

        <!-- Employee Table -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden mb-8">
            <div
                class="p-6 border-b border-gray-100 dark:border-gray-700 flex flex-col md:flex-row justify-between items-center gap-4">
                <h3 class="text-lg font-bold text-gray-700 dark:text-gray-200">Data Pegawai</h3>
                <div class="relative w-full md:w-64">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"><svg
                            class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg></span>
                    <input type="text" id="employee-search"
                        class="w-full pl-10 pr-4 py-2 text-sm text-gray-700 bg-gray-50 dark:bg-gray-700 dark:text-gray-200 border border-gray-200 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors"
                        placeholder="Cari nama pegawai...">
                </div>
            </div>
            <div id="employee-table-container">
                @include('partials.employee-table')
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        const colors = ['#3B82F6', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6', '#EC4899', '#6366F1'];
        const getChartColors = () => { const isDark = document.documentElement.classList.contains('dark'); return { chart: { foreColor: isDark ? '#f3f4f6' : '#374151', background: 'transparent' }, theme: { mode: isDark ? 'dark' : 'light' } }; };
        const chartInstances = {};
        var optionsJenikel = { series: @json($chartJenikel['series']), labels: @json($chartJenikel['labels']), chart: { type: 'pie', height: 350, ...getChartColors().chart }, theme: getChartColors().theme, colors: ['#0EA5E9', '#EC4899'], legend: { position: 'right', formatter: function (seriesName, opts) { return seriesName + ": " + opts.w.globals.series[opts.seriesIndex] } } };
        chartInstances.jenikel = new ApexCharts(document.querySelector("#chart-jenikel"), optionsJenikel);
        chartInstances.jenikel.render();
        var optionsStsPeg = { series: @json($chartStsPeg['series']), labels: @json($chartStsPeg['labels']), chart: { type: 'pie', height: 350, ...getChartColors().chart }, theme: getChartColors().theme, colors: ['#F59E0B', '#10B981', '#6366F1'], legend: { position: 'right', formatter: function (seriesName, opts) { return seriesName + ": " + opts.w.globals.series[opts.seriesIndex] } } };
        chartInstances.stsPeg = new ApexCharts(document.querySelector("#chart-sts-peg"), optionsStsPeg);
        chartInstances.stsPeg.render();
        var optionsPendidikan = { series: [{ name: 'Jumlah', data: @json($chartPendidikan['series']) }], chart: { type: 'bar', height: 350, ...getChartColors().chart }, theme: getChartColors().theme, xaxis: { categories: @json($chartPendidikan['categories']) }, plotOptions: { bar: { borderRadius: 4, horizontal: true } }, colors: ['#10B981'] };
        chartInstances.pendidikan = new ApexCharts(document.querySelector("#chart-pendidikan"), optionsPendidikan);
        chartInstances.pendidikan.render();
        var optionsEselon = { series: [{ name: 'Jumlah', data: @json($chartEselon['series']) }], chart: { type: 'bar', height: 350, ...getChartColors().chart }, theme: getChartColors().theme, xaxis: { categories: @json($chartEselon['categories']) }, plotOptions: { bar: { borderRadius: 4, horizontal: false } }, colors: ['#F59E0B'] };
        chartInstances.eselon = new ApexCharts(document.querySelector("#chart-eselon"), optionsEselon);
        chartInstances.eselon = new ApexCharts(document.querySelector("#chart-eselon"), optionsEselon);
        chartInstances.eselon.render();
        var optionsGolongan = { series: [{ name: 'Jumlah', data: @json($chartGolongan['series']) }], chart: { type: 'bar', height: 350, ...getChartColors().chart }, theme: getChartColors().theme, xaxis: { categories: @json($chartGolongan['categories']) }, plotOptions: { bar: { borderRadius: 4, horizontal: false } }, colors: ['#8B5CF6'] };
        chartInstances.golongan = new ApexCharts(document.querySelector("#chart-golongan"), optionsGolongan);
        chartInstances.golongan.render();
        var optionsGenerasi = { series: @json($chartGenerasi['series']), labels: @json($chartGenerasi['labels']), chart: { type: 'pie', height: 350, ...getChartColors().chart }, theme: getChartColors().theme, colors: ['#3B82F6', '#10B981', '#F59E0B', '#64748B'], legend: { position: 'right', formatter: function (seriesName, opts) { return seriesName + ": " + opts.w.globals.series[opts.seriesIndex] } } };
        chartInstances.generasi = new ApexCharts(document.querySelector("#chart-generasi"), optionsGenerasi);
        chartInstances.generasi.render();
        var optionsOpd = { series: [{ name: 'Jumlah', data: @json($chartOpd['series']) }], chart: { type: 'bar', height: 400, ...getChartColors().chart }, theme: getChartColors().theme, xaxis: { categories: @json($chartOpd['categories']) }, plotOptions: { bar: { borderRadius: 4, horizontal: true } }, colors: ['#6366F1'] };
        chartInstances.opd = new ApexCharts(document.querySelector("#chart-opd"), optionsOpd);
        chartInstances.opd.render();
        function updateChartTheme() { const newColors = getChartColors(); Object.values(chartInstances).forEach(chart => { chart.updateOptions({ chart: newColors.chart, theme: newColors.theme }); }); }
    </script>
@endsection