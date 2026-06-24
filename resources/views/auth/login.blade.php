<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Dashboard ASN</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-slate-50 dark:bg-zinc-950 flex items-center justify-center min-h-screen text-slate-800 dark:text-zinc-100 transition-colors duration-300">

    <div class="container mx-auto px-4">
        <div class="flex flex-wrap justify-center">
            <div class="w-full md:w-6/12 lg:w-4/12 px-4">
                <div class="card card-login bg-white dark:bg-zinc-900 rounded-2xl shadow-xl shadow-slate-200/50 dark:shadow-zinc-950/50 border border-slate-200/40 dark:border-zinc-800/40 overflow-hidden">
                    <div class="card-header-custom bg-indigo-600 dark:bg-indigo-700 text-white p-6 text-center">
                        <h4 class="mb-0 text-xl font-semibold tracking-tight">🔐 LOGIN PRISMA</h4>
                        <small class="text-indigo-100 dark:text-indigo-200">Portal Real-time Informasi & Sistem Manajemen Aparatur</small>
                    </div>
                    <div class="card-body p-8 bg-white dark:bg-zinc-900">



                        @if (session('error'))
                            <div class="alert alert-danger fade show bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800/50 text-red-700 dark:text-red-400 px-4 py-3 rounded-xl relative mb-6 flex items-center justify-between" role="alert">
                                <div>❌ {{ session('error') }}</div>
                                <button type="button" class="btn-close text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 focus:outline-none after:content-['✕'] after:text-lg font-bold" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        @if ($errors->any())
                            <div class="alert alert-danger bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800/50 text-red-700 dark:text-red-400 px-4 py-3 rounded-xl mb-6">
                                <ul class="mb-0 list-none p-0 m-0">
                                    @foreach ($errors->all() as $error)
                                        <li class="py-1 text-sm font-medium">⚠️ {{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('login.post') }}" method="POST">
                            @csrf

                            <div class="mb-5">
                                <label for="email" class="block form-label text-sm font-medium text-slate-600 dark:text-zinc-400 mb-2">Email Address</label>
                                <input type="email" name="email" class="form-control form-control-lg w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-zinc-800 bg-slate-50 dark:bg-zinc-900/50 text-slate-800 dark:text-zinc-100 focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 dark:focus:ring-indigo-500/30 transition-all outline-none"
                                    placeholder="admin@instansi.go.id" value="{{ old('email') }}" required autofocus>
                            </div>

                            <div class="mb-8">
                                <label for="password" class="block form-label text-sm font-medium text-slate-600 dark:text-zinc-400 mb-2">Password</label>
                                <input type="password" name="password" class="form-control form-control-lg w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-zinc-800 bg-slate-50 dark:bg-zinc-900/50 text-slate-800 dark:text-zinc-100 focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 dark:focus:ring-indigo-500/30 transition-all outline-none"
                                    placeholder="********" required>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-success btn-lg w-full flex justify-center items-center py-3.5 px-4 bg-indigo-600 hover:bg-indigo-500 text-white font-semibold rounded-xl shadow-sm shadow-indigo-600/20 hover:shadow-md hover:shadow-indigo-600/30 transition-all duration-200">Masuk Dashboard ➤</button>
                            </div>
                        </form>
                    </div>
                    <div class="card-footer text-center py-4 bg-slate-50 dark:bg-zinc-900/50 text-slate-500 dark:text-zinc-500 text-xs font-medium border-t border-slate-100 dark:border-zinc-800/60">
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