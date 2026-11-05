<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk Akun - T.A.M Laundry</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { 
            background: #f0f4f8; 
            min-height: 100vh; 
            display: flex; 
            align-items: center; 
            margin: 0;
        }
        .auth-card { border-radius: 16px; max-width: 420px; margin: 0 auto; }
    </style>
</head>
<body>
    <div class="container w-100">

        @if(session('sukses'))
            <div class="alert alert-success fw-bold text-center shadow-sm mb-3" style="max-width:420px;margin:0 auto 12px; border-radius: 12px;">
                ✅ {{ session('sukses') }}
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger fw-bold text-center shadow-sm mb-3" style="max-width:420px;margin:0 auto 12px; border-radius: 12px;">
                ❌ {{ session('error') }}
            </div>
        @endif

        <div class="card auth-card shadow border-0">
            <div class="card-body p-4 p-sm-5">

                <div class="text-center mb-4">
                    <h4 class="fw-bold text-success mb-0">🫧 T.A.M LAUNDRY</h4>
                    <p class="text-muted small mt-1">Silakan masuk untuk melanjutkan</p>
                </div>

                <form action="{{ route('customer.login.submit') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label class="fw-bold form-label">Nomor WhatsApp</label>
                        <input type="text" name="no_wa"
                               class="form-control @error('no_wa') is-invalid @enderror"
                               placeholder="Contoh: 08123456789"
                               value="{{ old('no_wa') }}" 
                               oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 12);"
                               required autofocus>
                        @error('no_wa')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="fw-bold form-label">Kata Sandi</label>
                        <input type="password" name="password"
                               class="form-control @error('password') is-invalid @enderror"
                               placeholder="••••••" required>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Lupa Password --}}
                    <div class="text-end mb-3">
                        <a href="{{ route('lupa.password') }}" class="small text-muted text-decoration-none">
                            Lupa kata sandi?
                        </a>
                    </div>

                    <button type="submit" class="btn btn-success w-100 fw-bold py-2">
                        Masuk Sistem
                    </button>
                </form>

                <hr class="my-3">

                <div class="text-center">
                    <span class="text-muted small">Belum punya akun? </span>
                    <a href="{{ route('register.pelanggan') }}" class="fw-bold text-success text-decoration-none small">
                        Daftar Sekarang
                    </a>
                </div>

                <div class="text-center mt-2">
                    <a href="{{ route('login.pegawai') }}" class="text-muted text-decoration-none" style="font-size:.75rem;">
                        🔒 Akses Pegawai Internal
                    </a>
                </div>

            </div>
        </div>

    </div>
</body>
</html>