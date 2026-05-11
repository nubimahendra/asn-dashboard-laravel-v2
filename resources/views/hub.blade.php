<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hub Layanan ASN</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #0f2027 0%, #203a43 50%, #2c5364 100%);
            min-height: 100vh;
            color: white;
        }

        .hub-header {
            padding: 20px 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            margin-bottom: 40px;
        }

        .module-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            height: 100%;
            cursor: pointer;
            text-decoration: none;
            color: inherit;
            display: block;
        }

        .module-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.4);
            color: inherit;
        }

        .card-header-custom {
            background-color: #800000;
            color: white;
            padding: 15px;
            text-align: center;
            font-weight: bold;
            font-size: 1.2rem;
            letter-spacing: 1px;
        }

        .card-body-custom {
            padding: 30px 20px;
            text-align: center;
        }

        .module-icon {
            font-size: 3rem;
            margin-bottom: 15px;
            color: #4CAF50;
        }
        
        .module-icon.mari {
            color: #FFC107;
        }

        .module-icon.mesra {
            color: #2196F3;
        }

        .module-title {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .module-desc {
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.95rem;
        }

        .btn-logout {
            background: rgba(255, 255, 255, 0.1);
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: all 0.2s;
        }
        .btn-logout:hover {
            background: rgba(255, 0, 0, 0.2);
            color: white;
            border-color: rgba(255, 0, 0, 0.5);
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Header -->
        <div class="hub-header d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-3">
                <div>
                    <h4 class="mb-0">PRISMA Hub</h4>
                    <small class="text-white-50">Portal Real-time Informasi & Sistem Manajemen Aparatur</small>
                </div>
            </div>
            <div class="d-flex align-items-center gap-3">
                <div class="text-end d-none d-md-block">
                    <div class="fw-bold">{{ auth()->user()->name ?? 'User' }}</div>
                    <small class="text-white-50">{{ auth()->user()->role ?? 'Admin' }}</small>
                </div>
                <form action="{{ route('logout') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-logout px-3">
                        Logout 🚪
                    </button>
                </form>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                ✅ {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="text-center mb-5">
            <h2 class="fw-light">Selamat Datang! Pilih Layanan</h2>
        </div>

        <!-- Modules Grid -->
        <div class="row g-4 justify-content-center">
            <!-- MASN Module -->
            <div class="col-md-4">
                <a href="{{ route('masn.dashboard') }}" class="module-card">
                    <div class="card-header-custom">MASN</div>
                    <div class="card-body-custom">
                        <div class="module-icon">📊</div>
                        <h3 class="module-title">Manajemen ASN</h3>
                        <p class="module-desc">Dashboard statistik ASN, master data pegawai, sinkronisasi data, dan snapshot laporan terkini.</p>
                    </div>
                </a>
            </div>

            <!-- MARI Module -->
            <div class="col-md-4">
                <a href="{{ route('mari.dashboard') }}" class="module-card">
                    <div class="card-header-custom">MARI</div>
                    <div class="card-body-custom">
                        <div class="module-icon mari">💰</div>
                        <h3 class="module-title">Manajemen Iuran</h3>
                        <p class="module-desc">Laporan iuran Korpri, manajemen kelas jabatan, mapping perbup, dan pengaturan tarif.</p>
                    </div>
                </a>
            </div>

            <!-- MESRA Module -->
            <div class="col-md-4">
                <a href="{{ route('mesra.dashboard') }}" class="module-card">
                    <div class="card-header-custom">MESRA</div>
                    <div class="card-body-custom">
                        <div class="module-icon mesra">✉️</div>
                        <h3 class="module-title">Manajemen Surat</h3>
                        <p class="module-desc">Pengelolaan surat masuk (inbox), layanan pengajuan cerai, dan helpdesk chat interaktif.</p>
                    </div>
                </a>
            </div>
        </div>

        <!-- Footer -->
        <div class="text-center mt-5 pt-4 text-white-50">
            <small>&copy; {{ date('Y') }} Tim IT BKPSDM Kab. Blitar</small>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        setTimeout(function () {
            let alerts = document.querySelectorAll('.alert');
            alerts.forEach(function (alert) {
                let bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 3000);
    </script>
</body>

</html>
