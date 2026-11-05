<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Pegawai - T.A.M Laundry</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Desain Profesional SaaS: Background Elegan & Vertically Centered */
        body { 
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); 
            min-height: 100vh; 
            display: flex; 
            align-items: center; /* Kunci agar pas di tengah layar */
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .auth-card { 
            border-radius: 1.25rem; 
            max-width: 420px; 
            margin: 0 auto; 
            background: #ffffff; 
            border: none; 
        }
        .form-control { 
            background: #f8f9fa; 
            border: 1px solid #dee2e6; 
            color: #212529; 
            border-radius: 0.5rem; 
        }
        .form-control:focus { 
            background: #ffffff; 
            border-color: #0d6efd; 
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.15); 
        }
        .form-label { 
            color: #495057; 
            font-size: 0.9rem; 
        }
    </style>
</head>
<body>
    <div class="container w-100">

        @if(session('error'))
            <div class="alert alert-danger fw-bold text-center shadow-sm mb-3" style="max-width:420px;margin:0 auto 12px; border-radius: 12px;">
                ❌ {{ session('error') }}
            </div>
        @endif

        <div class="card auth-card shadow-lg">
            <div class="card-body p-4 p-sm-5">

                <div class="text-center mb-4">
                    <h4 class="fw-bold text-primary mb-0">🔒 PORTAL PEGAWAI</h4>
                    <p class="text-muted small mt-1">Khusus Admin & Kurir T.A.M Laundry</p>
                </div>

                {{-- POST ke rute pegawai --}}
                <form action="{{ route('pegawai.login.submit') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label class="fw-bold form-label">Alamat Email</label>
                        <input type="email" name="email"
                               class="form-control px-3 py-2"
                               placeholder="admin@tamlaundry.com"
                               required autofocus>
                    </div>

                    <div class="mb-4">
                        <label class="fw-bold form-label">Kata Sandi</label>
                        <input type="password" name="password"
                               class="form-control px-3 py-2"
                               placeholder="••••••" required>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 fw-bold py-2 fs-5 rounded-pill shadow-sm">
                        Masuk
                    </button>
                </form>

                <hr class="my-4">

                {{-- Tombol Kembali dipindah ke bawah --}}
                <div class="text-center mt-2">
                    <a href="{{ url('/') }}" class="text-muted text-decoration-none small fw-bold">
                        ← Kembali ke Halaman Utama
                    </a>
                </div>

            </div>
        </div>

    </div>
</body>
</html>