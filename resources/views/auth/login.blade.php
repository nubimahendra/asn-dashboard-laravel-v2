<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Dashboard ASN</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #0f2027 0%, #203a43 50%, #2c5364 100%);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .card-login {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
            overflow: hidden;
        }

        .card-header-custom {
            background-color: #800000;
            /* Warna Hijau Khas Pemda/ASN */
            color: white;
            padding: 20px;
            text-align: center;
        }
    </style>
</head>

<body>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5 col-lg-4">
                <div class="card card-login">
                    <div class="card-header-custom">
                        <h4 class="mb-0">🔐 Dashboard-ASN</h4>
                        <small>Silakan Log in untuk akses dashboard</small>
                    </div>
                    <div class="card-body p-4 bg-white">

                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                ✅ {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                ❌ {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0 list-unstyled">
                                    @foreach ($errors->all() as $error)
                                        <li>⚠️ {{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('login.post') }}" method="POST">
                            @csrf

                            <div class="mb-3">
                                <label for="email" class="form-label text-muted">Email Address</label>
                                <input type="email" name="email" class="form-control form-control-lg"
                                    placeholder="admin@instansi.go.id" value="{{ old('email') }}" required autofocus>
                            </div>

                            <div class="mb-4">
                                <label for="password" class="form-label text-muted">Password</label>
                                <input type="password" name="password" class="form-control form-control-lg"
                                    placeholder="********" required>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-success btn-lg">Masuk Dashboard ➤</button>
                            </div>
                        </form>
                    </div>
                    <div class="card-footer text-center py-3 bg-light text-muted">
                        <small>&copy; {{ date('Y') }} Dashboard Kepegawaian</small>
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

</html>