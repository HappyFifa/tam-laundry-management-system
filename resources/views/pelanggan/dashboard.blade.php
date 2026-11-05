<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dasbor Saya - T.A.M Laundry</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f4f7f6; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .riwayat-card { border-radius: 20px; transition: transform 0.2s, box-shadow 0.2s; border: none; }
        .riwayat-card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.08); }
        .badge-status { border-radius: 12px; padding: 6px 12px; font-weight: 600; }
        .navbar-custom { background: linear-gradient(90deg, #198754 0%, #20c997 100%); border-radius: 0 0 20px 20px; padding: 15px 0;}
        .price-card { border-radius: 16px; border: none; transition: transform 0.2s; }
        .price-card:hover { transform: scale(1.02); }
    </style>
</head>
<body>

    @php
        \Carbon\Carbon::setLocale('id');
        function formatEWP($menit) {
            if ($menit <= 0) return 'Menunggu Timbangan';
            $hari = floor($menit / 1440);
            $sisa_menit = $menit % 1440;
            $jam = floor($sisa_menit / 60);
            $menit_akhir = $sisa_menit % 60;
            $hasil = '';
            if ($hari > 0) $hasil .= $hari . ' Hari ';
            if ($jam > 0) $hasil .= $jam . ' Jam ';
            if ($menit_akhir > 0) $hasil .= $menit_akhir . ' Menit';
            return trim($hasil);
        }
    @endphp

    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom shadow-sm mb-4">
        <div class="container">
            <a class="navbar-brand fw-bold fs-4">🫧 T.A.M Laundry</a>
            <form action="{{ route('logout') }}" method="POST" class="mb-0">
                @csrf
                <button class="btn btn-light text-success fw-bold rounded-pill px-4" type="submit">👋 Keluar</button>
            </form>
        </div>
    </nav>

    <div class="container pb-5">
        @if(session('sukses'))
            <div class="alert alert-success fw-bold rounded-4 shadow-sm text-center">🎉 {{ session('sukses') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger fw-bold rounded-4 shadow-sm text-center">❌ {{ session('error') }}</div>
        @endif

        <div class="row align-items-center mb-4 bg-white p-4 rounded-4 shadow-sm mx-0">
            <div class="col-md-8 text-center text-md-start mb-3 mb-md-0">
                <h3 class="fw-bold text-dark mb-1">Halo, {{ Auth::user()->name }}! ✨</h3>
                <p class="text-muted mb-0">Yuk pantau cucianmu atau buat pesanan baru hari ini.</p>
            </div>
            <div class="col-md-4 text-center text-md-end">
                <a href="{{ url('/') }}" class="btn btn-primary btn-lg fw-bold rounded-pill shadow-sm w-100">
                    + 🧺 Pesan Laundry Baru
                </a>
            </div>
        </div>

        <h5 class="fw-bold mb-3 ms-2 mt-5">💸 Katalog Layanan & Harga</h5>
        <div class="row g-3 mb-5 mx-0">
            <div class="col-md-4 col-sm-6">
                <div class="card price-card shadow-sm h-100 bg-primary-subtle text-primary">
                    <div class="card-body text-center p-4">
                        <div style="font-size: 2.5rem; margin-bottom:10px;">👕</div>
                        <h6 class="fw-bold mb-1">Cuci Kering</h6>
                        <h4 class="fw-bold mb-0">Rp 5.000<span class="fs-6 fw-normal">/kg</span></h4>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-sm-6">
                <div class="card price-card shadow-sm h-100 bg-success-subtle text-success">
                    <div class="card-body text-center p-4">
                        <div style="font-size: 2.5rem; margin-bottom:10px;">✨</div>
                        <h6 class="fw-bold mb-1">Cuci Setrika</h6>
                        <h4 class="fw-bold mb-0">Rp 7.000<span class="fs-6 fw-normal">/kg</span></h4>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-sm-12">
                <div class="card price-card shadow-sm h-100 bg-warning-subtle text-dark">
                    <div class="card-body text-center p-4">
                        <div style="font-size: 2.5rem; margin-bottom:10px;">🧼</div>
                        <h6 class="fw-bold mb-1">Cuci Satuan</h6>
                        <div class="small fw-bold opacity-75 text-start mx-auto mt-2" style="max-width: 180px;">
                            <div class="d-flex justify-content-between"><span>Karpet</span> <span>Rp 50K</span></div>
                            <div class="d-flex justify-content-between"><span>Selimut</span> <span>Rp 20K</span></div>
                            <div class="d-flex justify-content-between"><span>Mukena</span> <span>Rp 10K</span></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 mt-2">
                <div class="alert alert-info rounded-pill text-center py-2 mb-0 small border-0 shadow-sm">
                    🛵 <strong>Layanan Antar Jemput:</strong> Tersedia dengan tambahan biaya operasional Rp 7.500/pesanan.
                </div>
            </div>
        </div>

        <h5 class="fw-bold mb-3 ms-2 mt-4">📦 Riwayat Cucianmu</h5>
        <div class="row g-4">
            @forelse($riwayat_cucian as $t)
            <div class="col-md-6 col-lg-4">
                <div class="card riwayat-card shadow-sm h-100 {{ $t->status_pengerjaan == 'Dibatalkan' ? 'opacity-75' : '' }}">
                    <div class="card-body p-4">
                        
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="text-muted fw-bold small">#{{ $t->no_resi }}</span>
                            @if($t->status_pengerjaan != 'Dibatalkan')
                                <span class="badge {{ ($t->pembayaran && $t->pembayaran->status_pembayaran == 'Lunas') ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }} badge-status">
                                    {{ ($t->pembayaran && $t->pembayaran->status_pembayaran == 'Lunas') ? '✅ Lunas' : '⏳ Belum Lunas' }}
                                </span>
                            @else
                                <span class="badge bg-secondary badge-status">❌ Dibatalkan</span>
                            @endif
                        </div>

                        <h5 class="fw-bold mb-1">{{ $t->layanan->nama_layanan }}</h5>
                        <p class="text-muted small mb-3">
                            📅 {{ \Carbon\Carbon::parse($t->created_at)->format('d M Y') }} • 
                            @if($t->metode_kirim == 'Bawa Sendiri')
                                🚶 Bawa Sendiri
                            @else
                                🛵 Kurir: {{ $t->nama_kurir ?? '-' }}
                            @endif
                        </p>

                        <div class="bg-light rounded-4 p-3 mb-3 text-center">
                            <span class="d-block small text-muted mb-1">Status Saat Ini:</span>
                            @php
                                $statusColor = match($t->status_pengerjaan) {
                                    'Menunggu Penjemputan' => 'text-warning',
                                    'Menunggu Timbangan'   => 'text-secondary',
                                    'Sedang Dicuci'        => 'text-primary',
                                    'Siap Diantar'         => 'text-info',
                                    'Selesai'              => 'text-success',
                                    'Dibatalkan'           => 'text-danger',
                                    default                => 'text-dark',
                                };
                            @endphp
                            <strong class="{{ $statusColor }} fs-5 d-block mb-2">{{ $t->status_pengerjaan }}</strong>
                            
                            @if($t->status_pengerjaan != 'Dibatalkan')
                                <div class="badge bg-dark-subtle text-dark p-2 w-100 text-start lh-base">
                                    @if($t->ewp_menit > 0)
                                        ⏱️ Waktu Pengerjaan: <span class="fw-bold text-primary">{{ formatEWP($t->ewp_menit) }}</span><br>
                                        @if($t->status_pengerjaan != 'Selesai')
                                            📅 Jadwal Ambil/Antar:<br><span class="fw-bold text-info">{{ \Carbon\Carbon::parse($t->waktu_selesai_cuci)->translatedFormat('l, d M Y - H:i') }} WIB</span>
                                        @else
                                            📅 Selesai Pada:<br><span class="fw-bold text-success">{{ \Carbon\Carbon::parse($t->waktu_selesai_cuci)->translatedFormat('l, d M Y - H:i') }} WIB</span>
                                        @endif
                                    @else
                                        <div class="text-center w-100"><span class="fw-bold text-secondary">⏳ Menunggu Timbangan</span></div>
                                    @endif
                                </div>
                            @endif
                        </div>

                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fw-bold fs-5 {{ $t->status_pengerjaan == 'Dibatalkan' ? 'text-muted text-decoration-line-through' : 'text-success' }}">
                                Rp {{ number_format($t->pembayaran->total_harga ?? 0, 0, ',', '.') }}
                            </span>
                            <a href="{{ route('struk', $t->no_resi) }}" class="btn btn-dark fw-bold rounded-pill px-4">
                                {{ (!$t->pembayaran || $t->pembayaran->status_pembayaran == 'Belum Lunas') && $t->status_pengerjaan != 'Dibatalkan' ? '💳 Bayar' : '🧾 Struk' }}
                            </a>
                        </div>

                        {{-- TOMBOL BATAL PESANAN --}}
                        @if(in_array($t->status_pengerjaan, ['Menunggu Penjemputan', 'Menunggu Timbangan']))
                            <hr class="my-3 opacity-25">
                            <form action="{{ url('/pelanggan/batal/' . $t->no_resi) }}" method="POST" onsubmit="return confirm('⚠️ Yakin ingin membatalkan pesanan ini? Aksi ini tidak dapat dikembalikan.');">
                                @csrf
                                <button type="submit" class="btn btn-outline-danger btn-sm w-100 rounded-pill fw-bold">❌ Batalkan Pesanan</button>
                            </form>
                        @endif

                    </div>
                </div>
            </div>
            @empty
            <div class="col-12 text-center py-5 bg-white rounded-4 shadow-sm">
                <div style="font-size:4rem;">🧺</div>
                <h4 class="fw-bold mt-3 text-muted">Keranjang masih kosong</h4>
                <p class="text-muted">Kamu belum pernah memesan layanan laundry kami.</p>
            </div>
            @endforelse
        </div>
    </div>
</body>
</html>