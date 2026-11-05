<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lacak Pesanan - T.A.M Laundry</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-success text-white text-center py-3">
                        <h4 class="mb-0">🔍 Lacak Status Cucian</h4>
                    </div>
                    <div class="card-body p-4">
                        <form action="{{ route('lacak.cari') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="fw-bold">No. WhatsApp</label>
                                <input type="text" name="no_wa" class="form-control" required placeholder="08123456789" value="{{ request('no_wa') }}">
                            </div>
                            <div class="mb-3">
                                <label class="fw-bold">Nomor Resi</label>
                                <input type="text" name="no_resi" class="form-control" required placeholder="TRX-XXXXX" value="{{ request('no_resi') }}">
                            </div>
                            <button type="submit" class="btn btn-success w-100 fw-bold">Cari Pesanan</button>
                        </form>
                    </div>
                </div>

                @if(isset($dicari))
                    @if($transaksi)
                        <div class="card shadow-sm border-0 border-top border-success border-4">
                            <div class="card-body">
                                <h5 class="fw-bold text-center mb-3">Detail Pesanan: {{ $transaksi->no_resi }}</h5>
                                <ul class="list-group list-group-flush mb-3">
                                    <li class="list-group-item d-flex justify-content-between">
                                        <span>Nama</span> <strong>{{ $transaksi->user->name }}</strong>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between">
                                        <span>Layanan</span> <strong>{{ $transaksi->layanan->nama_layanan }} ({{ $transaksi->total_berat ?? '?' }} Kg)</strong>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between">
                                        <span>Total Tagihan</span> <strong>Rp {{ number_format($transaksi->pembayaran->total_harga ?? 0, 0, ',', '.') }}</strong>
                                    </li>
                                </ul>
                                
                                <div class="text-center p-3 bg-light rounded">
                                    <span class="d-block text-muted mb-1">Status Saat Ini:</span>
                                    @if($transaksi->status_pengerjaan == 'Menunggu Penjemputan')
                                        <h4 class="text-warning fw-bold mb-0">🛵 {{ $transaksi->status_pengerjaan }}</h4>
                                    @elseif($transaksi->status_pengerjaan == 'Sedang Dicuci')
                                        <h4 class="text-primary fw-bold mb-0">🫧 {{ $transaksi->status_pengerjaan }}</h4>
                                    @elseif($transaksi->status_pengerjaan == 'Siap Diantar')
                                        <h4 class="text-info fw-bold mb-0">📦 {{ $transaksi->status_pengerjaan }}</h4>
                                    @else
                                        <h4 class="text-success fw-bold mb-0">✅ {{ $transaksi->status_pengerjaan }}</h4>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="alert alert-danger fw-bold text-center shadow-sm">
                            ❌ Pesanan tidak ditemukan. Pastikan No. WA dan Resi benar!
                        </div>
                    @endif
                @endif

                <div class="text-center mt-4">
                    <a href="{{ url('/') }}" class="text-decoration-none text-muted">&larr; Kembali ke Halaman Utama</a>
                </div>

            </div>
        </div>
    </div>
</body>
</html>