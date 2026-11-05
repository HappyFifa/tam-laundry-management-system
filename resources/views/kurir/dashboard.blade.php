<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Kurir - T.A.M Laundry</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <style>
        #map { height: 400px; width: 100%; border-radius: 0 0 8px 8px; z-index: 1;}
        @media (max-width: 768px) { #map { height: 280px; } }
        .badge-status-jemput { background-color: #ffc107; color: #000; }
        .badge-status-antar  { background-color: #0dcaf0; color: #000; }
        .task-card { border-left: 4px solid #0d6efd; transition: box-shadow .2s; }
        .task-card:hover { box-shadow: 0 4px 16px rgba(0,0,0,.10); }
        .gmaps-btn { font-size: .80rem; }
    </style>
</head>
<body class="bg-light">

    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
        <div class="container-fluid px-3">
            <a class="navbar-brand fw-bold" href="#">🛵 Panel Kurir T.A.M</a>
            <div class="d-flex align-items-center gap-2">
                <span class="text-white d-none d-sm-inline small">Halo, {{ Auth::user()->name }}</span>
                <form action="{{ route('logout') }}" method="POST" class="mb-0">
                    @csrf
                    <button class="btn btn-sm btn-danger fw-bold" type="submit">Logout</button>
                </form>
            </div>
        </div>
    </nav>

    <div class="container-fluid px-3 mt-3">
        @if(session('sukses'))
            <div class="alert alert-success fw-bold shadow-sm mb-3">✅ {{ session('sukses') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger fw-bold shadow-sm mb-3">❌ {{ session('error') }}</div>
        @endif

        <div class="row g-3">
            <div class="col-lg-8">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white fw-bold py-3 border-bottom">
                        📍 Peta Lokasi Tugas Aktif
                    </div>
                    <div id="map"></div>

                    <div class="card-footer bg-white border-top p-3">
                        <h6 class="fw-bold text-dark mb-2">🗺️ Navigasi ke Pelanggan (Akurat via Titik Koordinat):</h6>
                        <div class="row g-2">
                            @forelse($transaksi as $t)
                                @php
                                    $origin = urlencode("Jl. Awibitung Gg. Jembar 4 No.4, Cicadas, Kota Bandung, Jawa Barat");
                                    $destination = urlencode($t->alamat);
                                    if($t->latitude && $t->longitude){
                                        $destination = "{$t->latitude},{$t->longitude}";
                                    }
                                    $gmaps_link = "https://www.google.com/maps/dir/?api=1&origin={$origin}&destination={$destination}&travelmode=driving";
                                @endphp
                                <div class="col-sm-6">
                                    <a href="{{ $gmaps_link }}" target="_blank"
                                       class="btn btn-sm btn-outline-success w-100 fw-bold text-start text-truncate gmaps-btn">
                                        🚗 {{ $t->no_resi }} → {{ $t->user->name }}
                                    </a>
                                </div>
                            @empty
                                <div class="col-12">
                                    <p class="text-muted small mb-0"><em>Tidak ada tugas navigasi saat ini.</em></p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-white fw-bold py-3 border-bottom">
                        📋 Daftar Tugas Aktif
                        <span class="badge bg-primary rounded-pill float-end">{{ $transaksi->count() }}</span>
                    </div>
                    <div class="overflow-auto" style="max-height: 560px;">
                        @forelse($transaksi as $index => $t)
                        <div class="task-card card border-0 border-bottom m-0 rounded-0 p-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="badge bg-secondary">{{ $t->no_resi }}</span>
                                <span class="badge {{ $t->status_pengerjaan == 'Menunggu Penjemputan' ? 'badge-status-jemput' : 'badge-status-antar' }}">
                                    {{ $t->status_pengerjaan == 'Menunggu Penjemputan' ? '🛵 Jemput' : '📦 Antar' }}
                                </span>
                            </div>

                            <p class="fw-bold text-dark mb-1">{{ $t->user->name }}</p>
                            
                            @if($t->metode_pembayaran == 'Tunai' && (!$t->pembayaran || $t->pembayaran->status_pembayaran != 'Lunas'))
                                <div class="alert alert-warning small py-1 px-2 mt-1 mb-2 fw-bold text-dark border-0">
                                    💵 TAGIH TUNAI: Rp {{ number_format($t->pembayaran->total_harga ?? 0, 0, ',', '.') }}
                                </div>
                            @endif

                            @if($t->catatan)
                                <div class="alert alert-warning small py-1 px-2 mt-1 mb-2 fw-bold text-dark border-0">
                                    📝 Catatan: "{{ $t->catatan }}"
                                </div>
                            @endif

                            <p class="text-muted small mb-1">📱 {{ $t->user->no_wa }}</p>
                            <p class="text-muted small mb-1">🧺 {{ $t->layanan->nama_layanan }}</p>
                            <div class="bg-light rounded p-2 small text-muted mb-2">
                                <strong>Alamat:</strong> {{ $t->alamat ?? '-' }}
                            </div>

                            @if($t->latitude && $t->longitude)
                                <span class="badge bg-success-subtle text-success small mb-2">📌 Koordinat tersedia</span>
                            @else
                                <span class="badge bg-warning-subtle text-warning small mb-2">⚠️ Koordinat belum ada</span>
                            @endif

                            <div class="d-flex gap-2 mt-1 flex-column">
                                <div class="d-flex gap-2">
                                    @if($t->latitude && $t->longitude)
                                    <button class="btn btn-primary btn-sm fw-bold w-100" onclick="fokusKeLokasi({{ $index }})">
                                        🔍 Pin Peta
                                    </button>
                                    @else
                                    <button class="btn btn-secondary btn-sm fw-bold w-100" disabled>
                                        🔍 Tanpa Pin
                                    </button>
                                    @endif
                                </div>

                                <!-- PERUBAHAN: FORM DENGAN UPLOAD FOTO (ENCTYPE) -->
                                @if($t->status_pengerjaan == 'Menunggu Penjemputan')
                                <form action="{{ url('/kurir/konfirmasi-jemput/' . $t->no_resi) }}" method="POST" enctype="multipart/form-data" class="mb-0 border border-warning rounded p-2 bg-warning bg-opacity-10" onsubmit="return confirm('⚠️ Yakin sudah dijemput? Foto bukti akan tersimpan.');">
                                    @csrf
                                    <label class="small fw-bold mb-1">📸 Foto Bukti Jemput (Wajib)</label>
                                    <input type="file" name="foto_bukti" class="form-control form-control-sm mb-2" accept="image/*" capture="environment" required>
                                    <button type="submit" class="btn btn-warning btn-sm fw-bold w-100 text-dark">✅ Selesai Dijemput</button>
                                </form>
                                @elseif($t->status_pengerjaan == 'Siap Diantar')
                                <form action="{{ url('/kurir/konfirmasi-jemput/' . $t->no_resi) }}" method="POST" enctype="multipart/form-data" class="mb-0 border border-info rounded p-2 bg-info bg-opacity-10" onsubmit="return confirm('⚠️ Yakin sudah diantar? Foto bukti akan tersimpan.');">
                                    @csrf
                                    <label class="small fw-bold mb-1">📸 Foto Bukti Antar (Wajib)</label>
                                    <input type="file" name="foto_bukti" class="form-control form-control-sm mb-2" accept="image/*" capture="environment" required>
                                    <button type="submit" class="btn btn-info btn-sm fw-bold w-100 text-dark">🏁 Selesai Diantar</button>
                                </form>
                                @endif
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-5 text-muted">
                            <div style="font-size:2rem;">🎉</div>
                            <p class="mt-2"><em>Tidak ada tugas aktif saat ini.</em></p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        var map = L.map('map').setView([-6.908795278171104, 107.63908969612783], 16);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap'
        }).addTo(map);

        var iconPusat = L.divIcon({
            html: '<div style="background:#0d6efd;color:white;border-radius:50%;width:32px;height:32px;display:flex;align-items:center;justify-content:center;font-size:16px;border:2px solid white;box-shadow:0 2px 6px rgba(0,0,0,.3)">🏢</div>',
            className: '', iconSize: [32, 32], iconAnchor: [16, 16]
        });
        
        L.marker([-6.908795278171104, 107.63908969612783], { icon: iconPusat })
            .addTo(map)
            .bindPopup('<b>🏢 T.A.M LAUNDRY</b><br>Jl. Awibitung Gg. Jembar 4 No.4 RT 4 RW 5<br>Kel. Cicadas, Kec. Cibeunying Kidul, Kota Bandung')
            .openPopup();

        var lokasiPelanggan = [];
        var markers = [];

        @foreach($transaksi as $index => $t)
            @if($t->latitude && $t->longitude)
                lokasiPelanggan[{{ $index }}] = [{{ $t->latitude }}, {{ $t->longitude }}];

                var iconPelanggan{{ $index }} = L.divIcon({
                    html: '<div style="background:#198754;color:white;border-radius:50%;width:28px;height:28px;display:flex;align-items:center;justify-content:center;font-size:14px;border:2px solid white;box-shadow:0 2px 6px rgba(0,0,0,.3)">📌</div>',
                    className: '', iconSize: [28, 28], iconAnchor: [14, 14]
                });

                markers[{{ $index }}] = L.marker(
                    [{{ $t->latitude }}, {{ $t->longitude }}],
                    { icon: iconPelanggan{{ $index }} }
                ).addTo(map).bindPopup(
                    '<b>📌 {{ $t->user->name }}</b><br>' +
                    'Resi: <strong>{{ $t->no_resi }}</strong><br>' +
                    '{{ $t->status_pengerjaan }}<br>' +
                    '<small class="text-muted">{{ $t->alamat }}</small>'
                );
            @else
                lokasiPelanggan[{{ $index }}] = null;
                markers[{{ $index }}] = null;
            @endif
        @endforeach

        function fokusKeLokasi(index) {
            if (lokasiPelanggan[index] && markers[index]) {
                map.setView(lokasiPelanggan[index], 16);
                markers[index].openPopup();
            } else {
                alert('Koordinat lokasi pelanggan ini belum tersedia.');
            }
        }
    </script>
</body>
</html>