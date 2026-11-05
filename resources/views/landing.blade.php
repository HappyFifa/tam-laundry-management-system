<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>T.A.M Laundry - Bersih, Wangi, Praktis</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .hero-bg { background: linear-gradient(135deg, #0d6efd 0%, #0dcaf0 100%); color: white; border-radius: 0 0 40px 40px; }
        .feature-icon { font-size: 3rem; margin-bottom: 15px; }
    </style>
</head>
<body class="bg-light">

    {{-- Hero Section --}}
    <div class="hero-bg text-center py-5 shadow-sm">
        <div class="container py-5">
            <h1 class="display-4 fw-bold mb-3">🫧 T.A.M Laundry</h1>
            <p class="lead mb-4">Solusi praktis untuk pakaian bersih dan wangi. Nikmati layanan antar-jemput dan pantau status cucian Anda secara real-time!</p>
            <a href="{{ route('login') }}" class="btn btn-light btn-lg fw-bold text-primary px-4 py-2 me-2 shadow-sm">Masuk / Pesan Sekarang</a>
            <a href="{{ route('register.pelanggan') }}" class="btn btn-outline-light btn-lg fw-bold px-4 py-2">Daftar Akun</a>
        </div>
    </div>

    {{-- Keunggulan --}}
    <div class="container mt-5 pt-3 mb-5">
        <div class="row text-center g-4">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100 p-4 rounded-4">
                    <div class="feature-icon">🛵</div>
                    <h5 class="fw-bold">Antar Jemput</h5>
                    <p class="text-muted small">Kurir kami siap menjemput pakaian kotor langsung dari depan pintu rumah Anda.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100 p-4 rounded-4">
                    <div class="feature-icon">⏱️</div>
                    <h5 class="fw-bold">Estimasi</h5>
                    <p class="text-muted small">Sistem kami menghitung waktu selesai secara otomatis.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100 p-4 rounded-4">
                    <div class="feature-icon">💳</div>
                    <h5 class="fw-bold">Bayar via QRIS</h5>
                    <p class="text-muted small">Transaksi lebih mudah, cepat, dan aman menggunakan dompet digital atau m-banking.</p>
                </div>
            </div>
        </div>
    </div>

    {{-- KATALOG HARGA (SYARAT DUITKU POIN 1) --}}
    <div class="container mb-5 pb-4">
        <h3 class="text-center fw-bold mb-4">Katalog Layanan & Harga</h3>
        <div class="row text-center g-4">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100 p-4 rounded-4" style="background-color: #e3f2fd;">
                    <h5 class="fw-bold text-primary">👕 Cuci Kering</h5>
                    <h3 class="fw-bold my-3">Rp 5.000<span class="fs-6 text-muted fw-normal"> / kg</span></h3>
                    <p class="text-muted small">Pakaian dicuci bersih dan dikeringkan (tanpa setrika).</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100 p-4 rounded-4" style="background-color: #d1e7dd;">
                    <h5 class="fw-bold text-success">✨ Cuci Setrika</h5>
                    <h3 class="fw-bold my-3">Rp 7.000<span class="fs-6 text-muted fw-normal"> / kg</span></h3>
                    <p class="text-muted small">Pakaian dicuci bersih, dikeringkan, dan disetrika rapi.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100 p-4 rounded-4" style="background-color: #fff3cd;">
                    <h5 class="fw-bold text-warning" style="color: #ffb300!important;">🧥 Cuci Satuan</h5>
                    <h3 class="fw-bold my-3">Mulai Rp 10.000<span class="fs-6 text-muted fw-normal"> / pcs</span></h3>
                    <p class="text-muted small">Mukena (Rp 10.000), Selimut (Rp 20.000), Karpet (Rp 50.000).</p>
                </div>
            </div>
        </div>
    </div>

    {{-- FOOTER KONTAK SUPPORT (SYARAT DUITKU POIN 2) --}}
    <footer class="bg-dark text-white pt-5 pb-4 mt-auto">
        <div class="container text-center text-md-start">
            <div class="row text-center text-md-start">
                <div class="col-md-6 col-lg-6 col-xl-6 mx-auto mt-3">
                    <h5 class="text-uppercase mb-4 font-weight-bold text-info">T.A.M Laundry</h5>
                    <p>Solusi praktis untuk pakaian bersih dan wangi. Kami terintegrasi dengan layanan Payment Gateway resmi untuk menjamin keamanan transaksi pembayaran online Anda.</p>
                </div>
                <div class="col-md-6 col-lg-5 col-xl-5 mx-auto mt-3">
                    <h5 class="text-uppercase mb-4 font-weight-bold text-info">Kontak Support</h5>
                    <p class="mb-2">📍 Jl. Awibitung Gg. Jembar 4 No. 4, RT 04 RW 05, Cicadas, Bandung.</p>
                    <p class="mb-2">📧 fikrizw356@gmail.com</p>
                    <p class="mb-2">📞 08212989052 (WhatsApp / Telepon)</p>
                </div>
            </div>
            <hr class="mb-4">
            <div class="row align-items-center">
                <div class="col-md-12 col-lg-12 text-center text-muted small">
                    <p>&copy; 2026 T.A.M Laundry. All rights reserved.</p>
                </div>
            </div>
        </div>
    </footer>

</body>
</html>