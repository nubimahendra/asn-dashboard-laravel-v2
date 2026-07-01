<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hub Layanan ASN</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gradient-to-br from-slate-900 via-zinc-900 to-slate-950 min-h-screen text-slate-100 selection:bg-indigo-500/30">
    <div class="container mx-auto px-4 md:px-8">
        <!-- Header -->
        <div class="hub-header flex justify-between items-center py-6 md:py-8 border-b border-white/10 mb-10 md:mb-16">
            <div class="flex items-center gap-3">
                <div>
                    <h4 class="mb-0 text-xl font-bold tracking-tight text-white">PRISMA Hub</h4>
                    <small class="text-slate-400 font-medium">Portal Real-time Informasi & Sistem Manajemen Aparatur</small>
                </div>
            </div>
            <div class="flex items-center gap-4">
                <div class="text-right hidden md:block">
                    <div class="font-semibold text-slate-100">{{ auth()->user()->name ?? 'User' }}</div>
                    <small class="text-slate-400 capitalize">{{ auth()->user()->role ?? 'Admin' }}</small>
                </div>
                @include('partials._user-dropdown')
            </div>
        </div>

        <div class="text-center mb-12">
            <h2 class="font-light text-3xl text-slate-200">Selamat Datang, Silahkan Pilih Layanan!!</h2>
        </div>

        <!-- Modules Grid -->
        <div class="row flex flex-wrap -mx-4 justify-center gap-y-8">
            <!-- MASN Module -->
            <div class="col-md-4 w-full md:w-1/2 lg:w-1/4 px-4">
                @if(auth()->user()->hasModuleAccess('masn'))
                <a href="{{ route('masn.dashboard') }}" class="module-card group block h-full bg-white/5 backdrop-blur-md border border-white/10 rounded-2xl overflow-hidden transition-all duration-500 hover:-translate-y-2 hover:shadow-[0_20px_40px_rgba(0,0,0,0.4)] hover:border-white/20 hover:bg-white/10">
                @else
                <div class="module-card opacity-50 cursor-not-allowed block h-full bg-white/5 backdrop-blur-md border border-white/10 rounded-2xl overflow-hidden" title="Hubungi admin untuk akses modul ini">
                @endif
                    <div class="card-header-custom bg-rose-900/80 backdrop-blur-sm text-white p-4 text-center font-bold text-lg tracking-wide border-b border-white/10">MASN</div>
                    <div class="card-body-custom p-8 text-center flex flex-col items-center">
                        <div class="module-icon text-5xl mb-5 text-emerald-400 group-hover:scale-110 transition-transform duration-500">📊</div>
                        <h3 class="module-title text-xl font-semibold mb-3 text-slate-100">Statistik ASN</h3>
                        <p class="module-desc text-slate-400 text-sm leading-relaxed">Dashboard statistik ASN, master data pegawai, sinkronisasi data, dan snapshot laporan terkini.</p>
                        @if(!auth()->user()->hasModuleAccess('masn'))
                            <span class="badge bg-slate-800 text-slate-300 text-xs px-3 py-1 rounded-full mt-4 border border-white/10">Akses Terkunci</span>
                        @endif
                    </div>
                @if(auth()->user()->hasModuleAccess('masn'))
                </a>
                @else
                </div>
                @endif
            </div>

            <!-- MARI Module -->
            <div class="col-md-4 w-full md:w-1/2 lg:w-1/4 px-4">
                @if(auth()->user()->hasModuleAccess('mari'))
                <a href="{{ route('mari.dashboard') }}" class="module-card group block h-full bg-white/5 backdrop-blur-md border border-white/10 rounded-2xl overflow-hidden transition-all duration-500 hover:-translate-y-2 hover:shadow-[0_20px_40px_rgba(0,0,0,0.4)] hover:border-white/20 hover:bg-white/10">
                @else
                <div class="module-card opacity-50 cursor-not-allowed block h-full bg-white/5 backdrop-blur-md border border-white/10 rounded-2xl overflow-hidden" title="Hubungi admin untuk akses modul ini">
                @endif
                    <div class="card-header-custom bg-indigo-900/80 backdrop-blur-sm text-white p-4 text-center font-bold text-lg tracking-wide border-b border-white/10">MARI</div>
                    <div class="card-body-custom p-8 text-center flex flex-col items-center">
                        <div class="module-icon mari text-5xl mb-5 text-amber-400 group-hover:scale-110 transition-transform duration-500">💰</div>
                        <h3 class="module-title text-xl font-semibold mb-3 text-slate-100">Manajemen Iuran</h3>
                        <p class="module-desc text-slate-400 text-sm leading-relaxed">Laporan iuran Korpri, manajemen kelas jabatan, mapping perbup, dan pengaturan tarif.</p>
                        @if(!auth()->user()->hasModuleAccess('mari'))
                            <span class="badge bg-slate-800 text-slate-300 text-xs px-3 py-1 rounded-full mt-4 border border-white/10">Akses Terkunci</span>
                        @endif
                    </div>
                @if(auth()->user()->hasModuleAccess('mari'))
                </a>
                @else
                </div>
                @endif
            </div>

            <!-- MESRA Module -->
            <div class="col-md-4 w-full md:w-1/2 lg:w-1/4 px-4">
                @if(auth()->user()->hasModuleAccess('mesra'))
                <a href="{{ route('mesra.dashboard') }}" class="module-card group block h-full bg-white/5 backdrop-blur-md border border-white/10 rounded-2xl overflow-hidden transition-all duration-500 hover:-translate-y-2 hover:shadow-[0_20px_40px_rgba(0,0,0,0.4)] hover:border-white/20 hover:bg-white/10">
                @else
                <div class="module-card opacity-50 cursor-not-allowed block h-full bg-white/5 backdrop-blur-md border border-white/10 rounded-2xl overflow-hidden" title="Hubungi admin untuk akses modul ini">
                @endif
                    <div class="card-header-custom bg-blue-900/80 backdrop-blur-sm text-white p-4 text-center font-bold text-lg tracking-wide border-b border-white/10">MESRA</div>
                    <div class="card-body-custom p-8 text-center flex flex-col items-center">
                        <div class="module-icon mesra text-5xl mb-5 text-blue-400 group-hover:scale-110 transition-transform duration-500">✉️</div>
                        <h3 class="module-title text-xl font-semibold mb-3 text-slate-100">Manajemen Surat</h3>
                        <p class="module-desc text-slate-400 text-sm leading-relaxed">Pengelolaan surat masuk (inbox), layanan pengajuan cerai, dan helpdesk chat interaktif.</p>
                        @if(!auth()->user()->hasModuleAccess('mesra'))
                            <span class="badge bg-slate-800 text-slate-300 text-xs px-3 py-1 rounded-full mt-4 border border-white/10">Akses Terkunci</span>
                        @endif
                    </div>
                @if(auth()->user()->hasModuleAccess('mesra'))
                </a>
                @else
                </div>
                @endif
            </div>

            <!-- SIPUT Module -->
            <div class="col-md-4 w-full md:w-1/2 lg:w-1/4 px-4">
                @if(auth()->user()->hasModuleAccess('siput'))
                <a href="{{ route('siput.dashboard') }}" class="module-card group block h-full bg-white/5 backdrop-blur-md border border-white/10 rounded-2xl overflow-hidden transition-all duration-500 hover:-translate-y-2 hover:shadow-[0_20px_40px_rgba(0,0,0,0.4)] hover:border-white/20 hover:bg-white/10">
                @else
                <div class="module-card opacity-50 cursor-not-allowed block h-full bg-white/5 backdrop-blur-md border border-white/10 rounded-2xl overflow-hidden" title="Hubungi admin untuk akses modul ini">
                @endif
                    <div class="card-header-custom bg-emerald-900/80 backdrop-blur-sm text-white p-4 text-center font-bold text-lg tracking-wide border-b border-white/10">SIPUT</div>
                    <div class="card-body-custom p-8 text-center flex flex-col items-center">
                        <div class="module-icon text-5xl mb-5 text-emerald-400 group-hover:scale-110 transition-transform duration-500">🏅</div>
                        <h3 class="module-title text-xl font-semibold mb-3 text-slate-100">Pengusulan SLKS</h3>
                        <p class="module-desc text-slate-400 text-sm leading-relaxed">Input data usul Satyalancana Karya Satya, rekomendasi otomatis, dan tracking usulan.</p>
                        @if(!auth()->user()->hasModuleAccess('siput'))
                            <span class="badge bg-slate-800 text-slate-300 text-xs px-3 py-1 rounded-full mt-4 border border-white/10">Akses Terkunci</span>
                        @endif
                    </div>
                @if(auth()->user()->hasModuleAccess('siput'))
                </a>
                @else
                </div>
                @endif
            </div>
        </div>

        <!-- Footer -->
        <div class="text-center mt-12 pt-6 text-slate-500 font-medium">
            <small>&copy; {{ date('Y') }} Tim IT BKPSDM Kab. Blitar</small>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Toast Notification -->
    @include('components.toast-notification')
</body>

</html>
