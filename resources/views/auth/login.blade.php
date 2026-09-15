<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Dashboard ASN</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-cover bg-center bg-no-repeat bg-fixed flex items-center min-h-screen text-slate-800 dark:text-zinc-100 transition-colors duration-300 relative overflow-hidden" style="background-image: url('{{ asset('images/bg-login.png') }}');">

    <!-- Overlay gelap tipis agar teks form tetap terbaca jika wallpaper terlalu terang -->
    <div class="absolute inset-0 bg-black/20 dark:bg-black/40 z-0"></div>

    <div class="container mx-auto px-4 sm:px-8 lg:px-16 relative z-10">
        <div class="flex flex-wrap justify-end">
            <!-- Menambahkan translate-x untuk menggeser form ke kanan sesuai request -->
            <div class="w-full md:w-6/12 lg:w-5/12 xl:w-4/12 translate-x-2 sm:translate-x-4 lg:translate-x-8 xl:translate-x-10">
                <div class="card card-login bg-white/20 dark:bg-black/30 backdrop-blur-xl rounded-3xl shadow-2xl shadow-black/30 border border-white/30 dark:border-white/10 overflow-hidden animate-slide-up">
                    <div class="card-header-custom bg-white/20 dark:bg-white/5 border-b border-white/20 dark:border-white/10 p-6 text-center backdrop-blur-md">
                        <h4 class="mb-0 text-2xl font-bold tracking-tight text-white drop-shadow-md">🔐 LOGIN DASHBOARD</h4>
                       <!-- <small class="text-indigo-50 font-medium drop-shadow">Portal Real-time Informasi & Sistem Manajemen Aparatur</small>-->
                    </div>
                    <div class="card-body p-8">

                        @if (session('error'))
                            <div class="alert alert-danger fade show bg-red-100/80 dark:bg-red-900/40 backdrop-blur-md border border-red-200 dark:border-red-800/50 text-red-700 dark:text-red-300 px-4 py-3 rounded-xl relative mb-6 flex items-center justify-between shadow-sm" role="alert">
                                <div class="font-medium">❌ {{ session('error') }}</div>
                                <button type="button" class="btn-close text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 focus:outline-none after:content-['✕'] after:text-lg font-bold" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        @if ($errors->any())
                            <div class="alert alert-danger bg-red-100/80 dark:bg-red-900/40 backdrop-blur-md border border-red-200 dark:border-red-800/50 text-red-700 dark:text-red-300 px-4 py-3 rounded-xl mb-6 shadow-sm">
                                <ul class="mb-0 list-none p-0 m-0">
                                    @foreach ($errors->all() as $error)
                                        <li class="py-1 text-sm font-medium">⚠️ {{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('login.post') }}" method="POST">
                            @csrf

                            <div class="relative mb-6">
                                <input type="email" name="email" id="email" class="peer form-control form-control-lg w-full px-4 pt-7 pb-2 rounded-xl border border-white/50 dark:border-white/10 bg-white/50 dark:bg-black/30 text-slate-800 dark:text-zinc-100 focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 transition-all outline-none backdrop-blur-md shadow-inner"
                                    placeholder=" " value="{{ old('email') }}" required autofocus>
                                <label for="email" class="absolute text-sm text-slate-500 dark:text-zinc-400 duration-300 transform -translate-y-3 scale-75 top-4 z-10 origin-[0] left-4 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-3 peer-focus:text-indigo-600 dark:peer-focus:text-indigo-300 pointer-events-none font-medium">Email Address</label>
                            </div>

                            <div class="relative mb-8">
                                <input type="password" name="password" id="password" class="peer form-control form-control-lg w-full px-4 pt-7 pb-2 rounded-xl border border-white/50 dark:border-white/10 bg-white/50 dark:bg-black/30 text-slate-800 dark:text-zinc-100 focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 transition-all outline-none backdrop-blur-md shadow-inner"
                                    placeholder=" " required>
                                <label for="password" class="absolute text-sm text-slate-500 dark:text-zinc-400 duration-300 transform -translate-y-3 scale-75 top-4 z-10 origin-[0] left-4 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-3 peer-focus:text-indigo-600 dark:peer-focus:text-indigo-300 pointer-events-none font-medium">Password</label>
                            </div>

                            <div class="d-grid gap-2 mt-2">
                                <button type="submit" class="btn btn-success btn-lg w-full flex justify-center items-center py-3.5 px-4 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-500 hover:to-purple-500 text-white font-bold rounded-xl shadow-lg shadow-indigo-600/30 hover:shadow-indigo-600/50 transform hover:-translate-y-0.5 transition-all duration-200 backdrop-blur-sm border border-indigo-400/30">Sign In</button>
                            </div>
                        </form>
                    </div>
                    <div class="card-footer text-center py-5 bg-white/20 dark:bg-black/20 text-slate-600 dark:text-zinc-400 text-xs font-medium border-t border-white/30 dark:border-white/5 backdrop-blur-md">
                        <small>&copy; {{ date('Y') }} Tim IT BKPSDM Kab. Blitar</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Auto-hide alerts after 3 seconds
    setTimeout(function () {
        let alerts = document.querySelectorAll('.alert');
        alerts.forEach(function (alert) {
            // Bootstrap 5 dismiss
            let bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 3000);
</script>

<!-- Toast Notification -->
@include('components.toast-notification')

</html>