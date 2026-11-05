<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Kata Sandi - T.A.M Laundry</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f0f4f8; }
        .auth-card { border-radius: 16px; max-width: 420px; margin: 0 auto; }
    </style>
</head>
<body class="pt-5 pb-5">
    <div class="container">

        @if(session('sukses'))
            <div class="alert alert-success fw-bold text-center shadow-sm mb-3" style="max-width:420px;margin:0 auto 12px;">
                ✅ {{ session('sukses') }}
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger fw-bold text-center shadow-sm mb-3" style="max-width:420px;margin:0 auto 12px;">
                ❌ {{ session('error') }}
            </div>
        @endif

        <div class="card auth-card shadow border-0">
            <div class="card-body p-4 p-sm-5">

                <div class="text-center mb-4">
                    <div style="font-size:2.5rem;">🔑</div>
                    <h5 class="fw-bold mb-0">Lupa Kata Sandi?</h5>
                    <p class="text-muted small mt-1">
                        Masukkan nomor WhatsApp Anda. Kami akan mengirimkan kata sandi Anda kembali.
                    </p>
                </div>

                <form action="{{ route('lupa.password.submit') }}" method="POST">
                    @csrf

                    <div class="mb-4">
                        <label class="fw-bold form-label">Nomor WhatsApp Terdaftar</label>
                        <input type="text" name="no_wa"
                               class="form-control @error('no_wa') is-invalid @enderror"
                               placeholder="08XXXXXXXXXX"
                               value="{{ old('no_wa') }}" required autofocus>
                        @error('no_wa')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-warning w-100 fw-bold py-2 text-dark">
                        📲 Kirim Ulang Kata Sandi via WhatsApp
                    </button>
                </form>

                <hr class="my-3">

                <div class="text-center">
                    <a href="{{ route('login') }}" class="text-muted text-decoration-none small">
                        ← Kembali ke halaman masuk
                    </a>
                </div>

            </div>
        </div>

    </div>
</body>
</html>
