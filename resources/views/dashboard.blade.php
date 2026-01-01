<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Statistik ASN</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            overflow-x: hidden;
        }

        /* Pembungkus Utama */
        #wrapper {
            display: flex;
            width: 100%;
            align-items: stretch;
        }
    </style>
</head>

<body class="bg-gray-50 dark:bg-gray-900 min-h-screen transition-colors duration-300">
    <div id="wrapper">
        <!-- Sidebar -->
        <aside id="main-sidebar"
            class="w-64 bg-white dark:bg-gray-800 shadow-md flex-col h-screen sticky top-0 z-20 flex-shrink-0 border-r dark:border-gray-700 hidden md:flex transition-all duration-300">
            <div
                class="p-6 border-b border-gray-100 dark:border-gray-700 flex-shrink-0 flex items-center justify-between">
                <h2 id="sidebar-title"
                    class="text-xl font-bold text-gray-800 dark:text-white flex items-center gap-2 whitespace-nowrap overflow-hidden transition-all duration-300">
                    <svg class="w-6 h-6 text-blue-600 flex-shrink-0" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                        </path>
                    </svg>
                    <span class="sidebar-text">Statistik ASN</span>
                </h2>
                <!-- Sidebar Toggle Button -->
                <button id="sidebar-toggle"
                    class="p-1 rounded-md text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 focus:outline-none ml-auto">
                    <svg id="toggle-icon" class="w-6 h-6 transform transition-transform duration-300" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7">
                        </path>
                    </svg>
                </button>
            </div>

            <!-- Filter Content Wrapper (Collapsible) -->
            <div id="sidebar-content" class="p-4 flex-shrink-0 transition-opacity duration-300 whitespace-nowrap">
                <label
                    class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">
                    Filter Unit Kerja
                </label>

                <!-- Custom Dropdown Component -->
                <div class="relative" id="opd-dropdown">
                    <!-- Dropdown Trigger button -->
                    <button type="button" id="dropdown-trigger"
                        class="w-full bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg shadow-sm px-4 py-2.5 text-left cursor-pointer focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all hover:bg-gray-50 dark:hover:bg-gray-600 flex items-center justify-between group">
                        <span
                            class="block truncate text-sm font-medium {{ $filterOpd ? 'text-blue-600 dark:text-blue-400' : 'text-gray-700 dark:text-gray-200' }}">
                            {{ $filterOpd ?? 'Pilih Unit Kerja' }}
                        </span>
                        <svg class="h-4 w-4 text-gray-400 dark:text-gray-500 group-hover:text-gray-600 dark:group-hover:text-gray-300 transition-colors"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7">
                            </path>
                        </svg>
                    </button>

                    <!-- Dropdown Menu -->
                    <div id="dropdown-menu"
                        class="hidden absolute z-50 mt-1 w-full bg-white dark:bg-gray-800 shadow-xl rounded-lg ring-1 ring-black ring-opacity-5 max-h-[60vh] flex flex-col overflow-hidden transform transition-all origin-top scale-95 opacity-0">

                        <!-- Sticky Search Box -->
                        <div
                            class="p-2 border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 sticky top-0 z-10">
                            <div class="relative">
                                <input type="text" id="opd-search" placeholder="Cari OPD..." autocomplete="off"
                                    class="w-full pl-9 pr-3 py-2 border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <!-- Scrollable List -->
                        <div class="overflow-y-auto flex-1 p-1 scrollbar-thin scrollbar-thumb-gray-200 dark:scrollbar-thumb-gray-600"
                            id="opd-list">
                            <a href="/"
                                class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-blue-50 dark:hover:bg-blue-900/50 hover:text-blue-700 dark:hover:text-blue-300 rounded-md transition-colors">
                                <span
                                    class="font-semibold text-blue-500 dark:text-blue-400 w-5 text-center mr-2">•</span>
                                Semua Unit Kerja
                            </a>

                            @foreach($listOpd as $opd)
                                <a href="?opd={{ urlencode($opd) }}"
                                    class="opd-item flex items-center px-4 py-2 text-sm text-gray-600 dark:text-gray-400 hover:bg-slate-50 dark:hover:bg-gray-700 hover:text-blue-600 dark:hover:text-blue-400 rounded-md transition-colors group"
                                    data-name="{{ strtolower($opd) }}">
                                    <span
                                        class="w-5 mr-2 flex-shrink-0 text-center {{ $filterOpd == $opd ? 'text-blue-500 dark:text-blue-400' : 'text-transparent group-hover:text-gray-300 dark:group-hover:text-gray-500' }}">
                                        <svg class="w-4 h-4 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    </span>
                                    <span class="truncate">{{ $opd }}</span>
                                </a>
                            @endforeach

                            <div id="no-results"
                                class="hidden px-4 py-6 text-center text-sm text-gray-400 dark:text-gray-500 italic">
                                Tidak ada OPD ditemukan
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Removed old static nav structure, keeping sidebar layout wrapper -->
            <div class="flex-1"></div>
        </aside>

        <!-- Main Content -->
        <!-- Main Content -->
        <main id="main-content"
            class="flex-1 bg-gray-50 dark:bg-gray-900 min-h-screen transition-all duration-300 w-full">
            <div class="container mx-auto px-6 py-8">
                <!-- Header -->
                <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
                    <div class="flex items-center gap-4">
                        <div>
                            <h1 class="text-3xl font-bold text-gray-800 dark:text-white">Dashboard Statistik ASN</h1>
                            <p class="text-gray-500 dark:text-gray-400 mt-1 flex items-center">
                                @if($filterOpd)
                                    <span
                                        class="bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 text-xs font-semibold px-2.5 py-0.5 rounded mr-2">FILTERED</span>
                                    Data Unit Kerja: {{ $filterOpd }}
                                @else
                                    Ringkasan Data Pegawai Aktif Pemerintah
                                @endif
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <!-- Dark Mode Toggle -->
                        <button id="theme-toggle"
                            class="p-2 rounded-lg bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 shadow-sm focus:outline-none focus:ring-2 focus:ring-yellow-500">
                            <!-- Sun Icon -->
                            <svg id="theme-toggle-light-icon" class="hidden w-6 h-6" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z">
                                </path>
                            </svg>
                            <!-- Moon Icon -->
                            <svg id="theme-toggle-dark-icon" class="hidden w-6 h-6" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z">
                                </path>
                            </svg>
                        </button>

                        @if($filterOpd)
                            <a href="/"
                                class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-200 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                                Reset Filter
                            </a>
                        @endif
                    </div>
                </div>

                <!-- Top Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <!-- Total Pegawai -->
                    <div
                        class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 border-l-4 border-blue-500 hover:shadow-md transition-shadow">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Total Pegawai</p>
                                <h2 class="text-3xl font-bold text-gray-800 dark:text-white">
                                    {{ number_format($totalPegawai) }}
                                </h2>
                            </div>
                            <div class="p-3 bg-blue-50 dark:bg-blue-900/30 rounded-full">
                                <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                                    </path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Total Laki-laki -->
                    <div
                        class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 border-l-4 border-teal-500 hover:shadow-md transition-shadow">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Laki-laki</p>
                                <h2 class="text-3xl font-bold text-gray-800 dark:text-white">
                                    {{ number_format($totalLaki) }}
                                </h2>
                            </div>
                            <div class="p-3 bg-teal-50 dark:bg-teal-900/30 rounded-full">
                                <svg class="w-6 h-6 text-teal-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Total Perempuan -->
                    <div
                        class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 border-l-4 border-pink-500 hover:shadow-md transition-shadow">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Perempuan</p>
                                <h2 class="text-3xl font-bold text-gray-800 dark:text-white">
                                    {{ number_format($totalPerempuan) }}
                                </h2>
                            </div>
                            <div class="p-3 bg-pink-50 dark:bg-pink-900/30 rounded-full">
                                <svg class="w-6 h-6 text-pink-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Rata-rata Usia -->
                    <div
                        class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 border-l-4 border-orange-500 hover:shadow-md transition-shadow">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Rata-rata Usia</p>
                                <h2 class="text-3xl font-bold text-gray-800 dark:text-white">
                                    {{ number_format($avgUsia, 1) }} Th
                                </h2>
                            </div>
                            <div class="p-3 bg-orange-50 dark:bg-orange-900/30 rounded-full">
                                <svg class="w-6 h-6 text-orange-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Charts Grid -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

                    <!-- Chart 1: Jenis Kelamin -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                        <h3 class="text-lg font-bold text-gray-700 dark:text-gray-200 mb-4">Pegawai per Jenis Kelamin
                        </h3>
                        <div id="chart-jenikel"></div>
                    </div>

                    <!-- Chart New: Jenis Pegawai -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                        <h3 class="text-lg font-bold text-gray-700 dark:text-gray-200 mb-4">Jenis Pegawai</h3>
                        <div id="chart-sts-peg"></div>
                    </div>

                    <!-- Chart 3: Pendidikan -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                        <h3 class="text-lg font-bold text-gray-700 dark:text-gray-200 mb-4">Pegawai per Pendidikan</h3>
                        <div id="chart-pendidikan"></div>
                    </div>

                    <!-- Chart 4: Usia -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                        <h3 class="text-lg font-bold text-gray-700 dark:text-gray-200 mb-4">Distribusi Usia</h3>
                        <div id="chart-usia"></div>
                    </div>

                    <!-- Chart 5: Jenis Jabatan -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                        <h3 class="text-lg font-bold text-gray-700 dark:text-gray-200 mb-4">Pegawai per Jenis Jabatan
                        </h3>
                        <div id="chart-jenis-jbt"></div>
                    </div>

                    <!-- Chart Moved: Golongan -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                        <h3 class="text-lg font-bold text-gray-700 dark:text-gray-200 mb-4">Pegawai per Golongan</h3>
                        <div id="chart-golongan"></div>
                    </div>

                    <!-- Chart 6: Unit Kerja (Show only if not filtered or show top always) -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 lg:col-span-2">
                        <h3 class="text-lg font-bold text-gray-700 dark:text-gray-200 mb-4">
                            @if($filterOpd)
                                Statistik Unit Kerja Ini
                            @else
                                Top 10 Unit Kerja / OPD
                            @endif
                        </h3>
                        <div id="chart-opd"></div>
                    </div>

                </div>
            </div>

            <!-- Mobile Sidebar Toggle Overlay (Simple Implementation if needed later, ignoring for now as requested desktop focus but keeping structure safe) -->
        </main>
    </div>

    <script>
        // Sidebar Dropdown & Search Functionality
        const dropdownTrigger = document.getElementById('dropdown-trigger');
        const dropdownMenu = document.getElementById('dropdown-menu');
        const searchInput = document.getElementById('opd-search');
        const opdList = document.getElementById('opd-list');
        const opdItems = opdList ? opdList.getElementsByClassName('opd-item') : [];
        const noResults = document.getElementById('no-results');

        // Toggle Dropdown
        if (dropdownTrigger && dropdownMenu) {
            dropdownTrigger.addEventListener('click', (e) => {
                e.stopPropagation();
                // Toggle visibility
                const isHidden = dropdownMenu.classList.contains('hidden');

                if (isHidden) {
                    dropdownMenu.classList.remove('hidden');
                    // Small delay to allow display:block to apply before opacity transition
                    requestAnimationFrame(() => {
                        dropdownMenu.classList.remove('scale-95', 'opacity-0');
                        dropdownMenu.classList.add('scale-100', 'opacity-100');
                    });
                    // Focus search
                    if (searchInput) setTimeout(() => searchInput.focus(), 100);
                } else {
                    closeDropdown();
                }
            });

            // Close on click outside
            document.addEventListener('click', (e) => {
                if (!dropdownMenu.contains(e.target) && !dropdownTrigger.contains(e.target)) {
                    closeDropdown();
                }
            });
        }

        function closeDropdown() {
            if (!dropdownMenu) return;
            dropdownMenu.classList.remove('scale-100', 'opacity-100');
            dropdownMenu.classList.add('scale-95', 'opacity-0');

            // Wait for transition to finish before hiding
            setTimeout(() => {
                dropdownMenu.classList.add('hidden');
            }, 200); // match transition duration roughly
        }

        // Search Filter
        if (searchInput) {
            searchInput.addEventListener('keyup', function (e) {
                const term = e.target.value.toLowerCase();
                let hasResults = false;

                Array.from(opdItems).forEach(item => {
                    const name = item.getAttribute('data-name');
                    if (name.includes(term)) {
                        item.classList.remove('hidden');
                        hasResults = true;
                    } else {
                        item.classList.add('hidden');
                    }
                });

                if (noResults) {
                    if (hasResults) {
                        noResults.classList.add('hidden');
                    } else {
                        noResults.classList.remove('hidden');
                    }
                }
            });

            // Prevent dropdown closing when clicking/typing in search
            searchInput.addEventListener('click', (e) => e.stopPropagation());
        }

        // --- UI Enhancements: Dark Mode & Sidebar Toggle ---

        // Dark Mode Logic
        const themeToggleBtn = document.getElementById('theme-toggle');
        const themeToggleDarkIcon = document.getElementById('theme-toggle-dark-icon');
        const themeToggleLightIcon = document.getElementById('theme-toggle-light-icon');

        // Check local storage or system preference
        if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
            themeToggleLightIcon.classList.remove('hidden');
        } else {
            document.documentElement.classList.remove('dark');
            themeToggleDarkIcon.classList.remove('hidden');
        }

        themeToggleBtn.addEventListener('click', function () {
            // toggle icons
            themeToggleDarkIcon.classList.toggle('hidden');
            themeToggleLightIcon.classList.toggle('hidden');

            // if set via local storage previously
            if (localStorage.getItem('color-theme')) {
                if (localStorage.getItem('color-theme') === 'light') {
                    document.documentElement.classList.add('dark');
                    localStorage.setItem('color-theme', 'dark');
                } else {
                    document.documentElement.classList.remove('dark');
                    localStorage.setItem('color-theme', 'light');
                }
            } else {
                if (document.documentElement.classList.contains('dark')) {
                    document.documentElement.classList.remove('dark');
                    localStorage.setItem('color-theme', 'light');
                } else {
                    document.documentElement.classList.add('dark');
                    localStorage.setItem('color-theme', 'dark');
                }
            }
            updateChartTheme(); // Function to update charts if needed
        });

        // Sidebar Toggle Logic (Mini Sidebar)
        const sidebarToggleBtn = document.getElementById('sidebar-toggle');
        const mainSidebar = document.getElementById('main-sidebar');
        const sidebarContent = document.getElementById('sidebar-content');
        const sidebarTexts = document.querySelectorAll('.sidebar-text');
        const toggleIcon = document.getElementById('toggle-icon');

        if (sidebarToggleBtn && mainSidebar) {
            sidebarToggleBtn.addEventListener('click', () => {
                const isCollapsed = mainSidebar.classList.contains('w-20');

                if (isCollapsed) {
                    // EXPAND
                    mainSidebar.classList.remove('w-20');
                    mainSidebar.classList.add('w-64');

                    // Show content
                    sidebarContent.classList.remove('opacity-0', 'pointer-events-none');
                    sidebarTexts.forEach(el => el.classList.remove('hidden'));

                    // Rotate Icon Back
                    toggleIcon.classList.remove('rotate-180');

                } else {
                    // COLLAPSE
                    mainSidebar.classList.remove('w-64');
                    mainSidebar.classList.add('w-20');

                    // Hide content
                    sidebarContent.classList.add('opacity-0', 'pointer-events-none');
                    sidebarTexts.forEach(el => el.classList.add('hidden'));

                    // Rotate Icon
                    toggleIcon.classList.add('rotate-180');
                }
            });
        }



        function updateChartTheme() {
            // Reload page is easiest to reset chart colors for dark mode, 
            // but for smooth exp we could updateApexOptions()
            // For now, let's keep it simple. Charts look okayish on dark mode usually.
            // Or force reload:
            // location.reload();
        }

        // Helper to get color palette
        const colors = ['#3B82F6', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6', '#EC4899', '#6366F1'];

        // Initialize Common Options
        const getChartColors = () => {
            const isDark = document.documentElement.classList.contains('dark');
            return {
                chart: {
                    foreColor: isDark ? '#f3f4f6' : '#374151',
                    background: 'transparent'
                },
                theme: {
                    mode: isDark ? 'dark' : 'light'
                }
            };
        };

        const chartInstances = {};

        // 1. Chart Jenikel (Pie)
        var optionsJenikel = {
            series: @json($chartJenikel['series']),
            labels: @json($chartJenikel['labels']),
            chart: { type: 'pie', height: 350, ...getChartColors().chart },
            theme: getChartColors().theme,
            colors: ['#0EA5E9', '#EC4899'],
            legend: { position: 'bottom' }
        };
        chartInstances.jenikel = new ApexCharts(document.querySelector("#chart-jenikel"), optionsJenikel);
        chartInstances.jenikel.render();

        // Chart New: Jenis Pegawai (Pie)
        var optionsStsPeg = {
            series: @json($chartStsPeg['series']),
            labels: @json($chartStsPeg['labels']),
            chart: { type: 'pie', height: 350, ...getChartColors().chart },
            theme: getChartColors().theme,
            colors: ['#F59E0B', '#10B981', '#6366F1'],
            legend: { position: 'bottom' }
        };
        chartInstances.stsPeg = new ApexCharts(document.querySelector("#chart-sts-peg"), optionsStsPeg);
        chartInstances.stsPeg.render();

        // 2. Chart Golongan (Bar)
        var optionsGolongan = {
            series: [{ name: 'Jumlah', data: @json($chartGolongan['series']) }],
            chart: { type: 'bar', height: 350, ...getChartColors().chart },
            theme: getChartColors().theme,
            xaxis: { categories: @json($chartGolongan['categories']) },
            plotOptions: { bar: { borderRadius: 4, horizontal: false } },
            colors: ['#3B82F6']
        };
        chartInstances.golongan = new ApexCharts(document.querySelector("#chart-golongan"), optionsGolongan);
        chartInstances.golongan.render();

        // 3. Chart Pendidikan (Bar)
        var optionsPendidikan = {
            series: [{ name: 'Jumlah', data: @json($chartPendidikan['series']) }],
            chart: { type: 'bar', height: 350, ...getChartColors().chart },
            theme: getChartColors().theme,
            xaxis: { categories: @json($chartPendidikan['categories']) },
            plotOptions: { bar: { borderRadius: 4, horizontal: true } },
            colors: ['#10B981']
        };
        chartInstances.pendidikan = new ApexCharts(document.querySelector("#chart-pendidikan"), optionsPendidikan);
        chartInstances.pendidikan.render();

        // 4. Chart Usia (Area)
        var optionsUsia = {
            series: [{ name: 'Jumlah', data: @json($chartUsia['series']) }],
            chart: { type: 'area', height: 350, ...getChartColors().chart },
            theme: getChartColors().theme,
            xaxis: { categories: @json($chartUsia['categories']) },
            dataLabels: { enabled: false },
            stroke: { curve: 'smooth' },
            colors: ['#F59E0B']
        };
        chartInstances.usia = new ApexCharts(document.querySelector("#chart-usia"), optionsUsia);
        chartInstances.usia.render();

        // 5. Chart Jenis Jabatan (Bar)
        var optionsJenisJbt = {
            series: [{ name: 'Jumlah', data: @json($chartJenisJbt['series']) }],
            chart: { type: 'bar', height: 350, ...getChartColors().chart },
            theme: getChartColors().theme,
            xaxis: { categories: @json($chartJenisJbt['categories']) },
            plotOptions: { bar: { borderRadius: 4, horizontal: false } },
            colors: ['#8B5CF6']
        };
        chartInstances.jenisJbt = new ApexCharts(document.querySelector("#chart-jenis-jbt"), optionsJenisJbt);
        chartInstances.jenisJbt.render();

        // 6. Chart OPD (Bar Horizontal)
        var optionsOpd = {
            series: [{ name: 'Jumlah', data: @json($chartOpd['series']) }],
            chart: { type: 'bar', height: 400, ...getChartColors().chart },
            theme: getChartColors().theme,
            xaxis: { categories: @json($chartOpd['categories']) },
            plotOptions: { bar: { borderRadius: 4, horizontal: true } },
            colors: ['#6366F1']
        };
        chartInstances.opd = new ApexCharts(document.querySelector("#chart-opd"), optionsOpd);
        chartInstances.opd.render();

        function updateChartTheme() {
            const newColors = getChartColors();
            Object.values(chartInstances).forEach(chart => {
                chart.updateOptions({
                    chart: newColors.chart,
                    theme: newColors.theme
                });
            });
        }
    </script>
</body>

</html>