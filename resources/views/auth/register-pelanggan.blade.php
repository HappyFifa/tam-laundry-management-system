<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun - T.A.M Laundry</title>
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

        <div class="card auth-card shadow border-0">
            <div class="card-body p-4 p-sm-5">

                <div class="text-center mb-4">
                    <h4 class="fw-bold text-primary mb-0">📋 DAFTAR PELANGGAN</h4>
                    <p class="text-muted small mt-1">Lengkapi data diri pendaftaran Anda</p>
                </div>

                <form action="{{ route('customer.register.submit') }}" method="POST">
                    @csrf

                    {{-- Nama --}}
                    <div class="mb-3">
                        <label class="fw-bold form-label">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" name="nama"
                               class="form-control @error('nama') is-invalid @enderror"
                               placeholder="Masukkan nama lengkap Anda"
                               value="{{ old('nama') }}" required>
                        @error('nama')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- No WA (Hanya Angka Maks 12) --}}
                    <div class="mb-3">
                        <label class="fw-bold form-label">Nomor WhatsApp Aktif <span class="text-danger">*</span></label>
                        <input type="text" name="no_wa"
                               class="form-control @error('no_wa') is-invalid @enderror"
                               placeholder="08XXXXXXXXXX (Maks 12 Angka)"
                               value="{{ old('no_wa') }}" 
                               oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 12);"
                               required>
                        @error('no_wa')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text text-muted small">
                            📱 Nomor ini digunakan untuk login dan menerima notifikasi otomatis.
                        </div>
                    </div>

                    {{-- Password --}}
                    <div class="mb-4">
                        <label class="fw-bold form-label">Buat Kata Sandi <span class="text-danger">*</span></label>
                        <input type="password" name="password"
                               class="form-control @error('password') is-invalid @enderror"
                               placeholder="Minimal 6 karakter" required>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text text-muted small">
                            🔒 Kata sandi akan dikirim ke WhatsApp Anda setelah registrasi.
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 fw-bold py-2">
                        ✅ Selesaikan Pendaftaran
                    </button>
                </form>

                <hr class="my-3">

                <div class="text-center">
                    <span class="text-muted small">Sudah punya akun? </span>
                    <a href="{{ route('login') }}" class="fw-bold text-primary text-decoration-none small">
                        ← Masuk di sini
                    </a>
                </div>

            </div>
        </div>

    </div>
</body>
</html>