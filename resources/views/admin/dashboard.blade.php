<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - T.A.M Laundry</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .stat-card { border-radius: 12px; }
        .table th { font-size: .85rem; }
        .table td { font-size: .88rem; vertical-align: middle; }
        .ewp-badge { font-size: .75rem; }
        @media (max-width: 768px) {
            .table-responsive { font-size: .80rem; }
        }
    </style>
</head>
<body class="bg-light">

    @php
        function formatEWPAdmin($menit) {
            if ($menit <= 0) return '0 Menit';
            $hari = floor($menit / 1440);
            $jam = floor(($menit % 1440) / 60);
            $mnt = $menit % 60;
            $res = '';
            if($hari>0) $res .= $hari.'H ';
            if($jam>0) $res .= $jam.'J ';
            if($mnt>0) $res .= $mnt.'M';
            return trim($res);
        }
    @endphp

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
        <div class="container-fluid px-3">
            <a class="navbar-brand fw-bold">👔 T.A.M Laundry — Admin</a>
            <div class="d-flex align-items-center gap-2">
                <span class="text-white d-none d-sm-inline small">{{ Auth::user()->name }}</span>
                <form action="{{ route('logout') }}" method="POST" class="mb-0">
                    @csrf
                    <button class="btn btn-sm btn-danger fw-bold" type="submit">Logout</button>
                </form>
            </div>
        </div>
    </nav>

    <div class="container-fluid px-3 mt-3">

        @if(session('sukses'))
            <div class="alert alert-success fw-bold shadow-sm">🎉 {{ session('sukses') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger fw-bold shadow-sm">❌ {{ session('error') }}</div>
        @endif

        {{-- FORM FILTER TANGGAL --}}
        <div class="card shadow-sm border-0 mb-3 bg-white">
            <div class="card-body py-3">
                <form action="{{ route('admin.dashboard') }}" method="GET" class="row align-items-end g-2">
                    <div class="col-md-4">
                        <label class="small fw-bold text-muted mb-1">Mulai Tanggal:</label>
                        <input type="date" name="start_date" class="form-control form-control-sm" value="{{ request('start_date') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="small fw-bold text-muted mb-1">Sampai Tanggal:</label>
                        <input type="date" name="end_date" class="form-control form-control-sm" value="{{ request('end_date') }}">
                    </div>
                    <div class="col-md-4 d-flex gap-2">
                        <button type="submit" class="btn btn-primary btn-sm fw-bold w-100">🔍 Filter Laporan</button>
                        @if(request('start_date') || request('end_date'))
                            <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary btn-sm fw-bold w-100">Reset</a>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-sm-6 col-lg-4">
                <div class="card stat-card border-0 shadow-sm bg-primary text-white p-3">
                    <small class="text-uppercase fw-bold opacity-75" style="font-size:.75rem;">💰 Total Pendapatan (Lunas)</small>
                    <h3 class="fw-bold mt-1 mb-0">Rp {{ number_format($total_pendapatan, 0, ',', '.') }}</h3>
                    @if(request('start_date') && request('end_date'))
                        <small class="opacity-75" style="font-size:0.7rem;">(Berdasarkan filter tanggal)</small>
                    @endif
                </div>
            </div>
            <div class="col-sm-6 col-lg-4">
                <div class="card stat-card border-0 shadow-sm bg-warning text-dark p-3">
                    <small class="text-uppercase fw-bold opacity-75" style="font-size:.75rem;">🧺 Antrean Cucian Aktif</small>
                    <h3 class="fw-bold mt-1 mb-0">{{ $pesanan_aktif }} Pesanan</h3>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card stat-card border-0 shadow-sm bg-success text-white p-3 h-100 d-flex flex-column justify-content-center">
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.export', ['start_date' => request('start_date'), 'end_date' => request('end_date')]) }}" class="btn btn-light btn-sm fw-bold flex-fill text-success">
                            📥 Unduh Laporan CSV
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-lg-8">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white fw-bold py-3 border-bottom">
                        🗂️ Manajemen Pesanan & Validasi Timbangan
                        @if(request('start_date') && request('end_date'))
                            <span class="badge bg-primary float-end">Menampilkan Filter Tanggal</span>
                        @endif
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0 text-center align-middle">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Tanggal & Resi</th>
                                        <th class="text-start">Pelanggan</th>
                                        <th>Layanan</th>
                                        <th>Aksi / Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($transaksi as $t)
                                    <tr>
                                        <td class="text-secondary small">
                                            <span class="d-block mb-1 text-muted">{{ \Carbon\Carbon::parse($t->created_at)->format('d/m/Y') }}</span>
                                            <strong class="text-dark">{{ $t->no_resi }}</strong>
                                        </td>
                                        <td class="text-start">
                                            <span class="fw-bold d-block">{{ $t->user->name }}</span>
                                            <small class="text-muted">{{ $t->user->no_wa }}</small>
                                            @if($t->catatan)
                                            <div class="alert alert-warning py-1 px-2 mt-1 mb-0 small fw-bold border-0" style="font-size: 0.75rem;">
                                                📝 "{{ $t->catatan }}"
                                            </div>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark border d-block mb-1">{{ $t->layanan->nama_layanan }}</span>
                                            <span class="badge bg-secondary small">{{ $t->metode_kirim }}</span>
                                        </td>
                                        <td>
                                            @if($t->status_pengerjaan == 'Dibatalkan')
                                                <span class="badge bg-danger text-white w-100 py-2">❌ Dibatalkan Pelanggan</span>
                                            @elseif($t->status_pengerjaan == 'Menunggu Penjemputan')
                                                <span class="badge bg-warning text-dark w-100 py-2">🛵 Kurir Sedang Menjemput</span>
                                                @if($t->nama_kurir)
                                                <small class="text-muted d-block mt-1">Kurir: {{ $t->nama_kurir }}</small>
                                                @endif
                                            @elseif($t->status_pengerjaan == 'Menunggu Timbangan')
                                                <form action="{{ url('/admin/input-berat/' . $t->no_resi) }}" method="POST" class="d-flex justify-content-center gap-1 align-items-center">
                                                    @csrf
                                                    <input type="number" step="0.1" min="0.1" name="berat" class="form-control form-control-sm" placeholder="Kg / Pcs" style="width:90px;" required>
                                                    <button type="submit" class="btn btn-danger btn-sm fw-bold text-nowrap">⚖️ Timbang</button>
                                                </form>
                                            @else
                                                
                                                @if($t->pembayaran && $t->pembayaran->status_pembayaran == 'Belum Lunas' && $t->metode_pembayaran == 'Tunai')
                                                    <form action="{{ url('/admin/konfirmasi-lunas/' . $t->no_resi) }}" method="POST" class="mb-2" onsubmit="return confirm('Yakin uang tunai sudah Anda terima dari kurir/pelanggan?');">
                                                        @csrf
                                                        <button type="submit" class="btn btn-success btn-sm w-100 fw-bold">💵 Tandai Lunas (Tunai)</button>
                                                    </form>
                                                @endif

                                                <form action="{{ url('/admin/update-status/' . $t->no_resi) }}" method="POST" class="d-flex justify-content-center gap-1 mb-1">
                                                    @csrf
                                                    <select name="status" class="form-select form-select-sm" style="width:130px;" required>
                                                        <option value="Sedang Dicuci"   {{ $t->status_pengerjaan == 'Sedang Dicuci'   ? 'selected' : '' }}>🫧 Sedang Dicuci</option>
                                                        <option value="Siap Diantar"    {{ $t->status_pengerjaan == 'Siap Diantar'    ? 'selected' : '' }}>📦 Siap Diantar</option>
                                                        <option value="Selesai"         {{ $t->status_pengerjaan == 'Selesai'         ? 'selected' : '' }}>✅ Selesai</option>
                                                    </select>
                                                    <button type="submit" class="btn btn-dark btn-sm fw-bold">OK</button>
                                                </form>

                                                <small class="text-success fw-bold d-block">
                                                    Rp {{ number_format($t->pembayaran->total_harga ?? 0, 0, ',', '.') }} | {{ $t->total_berat }} {{ str_contains(strtolower($t->layanan->nama_layanan), 'satuan') ? 'Pcs' : 'Kg' }}
                                                </small>
                                                
                                                <small class="text-muted fw-bold d-block ewp-badge mt-1">
                                                    ⏱ EWP: {{ formatEWPAdmin($t->ewp_menit) }}
                                                </small>

                                                @if($t->status_pengerjaan == 'Selesai')
                                                    <small class="text-success fw-bold ewp-badge d-block">
                                                        ✅ Selesai: {{ \Carbon\Carbon::parse($t->waktu_selesai_cuci)->format('d/m/y, H:i') }}
                                                    </small>
                                                @else
                                                    <small class="text-info fw-bold ewp-badge d-block">
                                                        📅 Target: {{ \Carbon\Carbon::parse($t->waktu_selesai_cuci)->format('d/m/y, H:i') }}
                                                    </small>
                                                @endif
                                            @endif
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-5 text-muted">
                                            <em>Belum ada data transaksi untuk tanggal tersebut.</em>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white fw-bold py-3 border-bottom text-primary">
                        📅 Transaksi Masuk Hari Ini
                    </div>
                    <div class="card-body p-0">
                        <ul class="list-group list-group-flush" style="max-height: 480px; overflow-y: auto;">
                            @forelse($laporan_hari_ini as $h)
                            <li class="list-group-item d-flex justify-content-between align-items-center py-3 px-3">
                                <div>
                                    <span class="fw-bold text-dark d-block">{{ $h->user->name }}</span>
                                    <small class="text-muted">{{ $h->no_resi }} ({{ $h->metode_pembayaran }})</small><br>
                                    <small class="text-muted">{{ $h->layanan->nama_layanan }}</small>
                                </div>
                                <div class="text-end">
                                    <span class="badge {{ ($h->pembayaran->status_pembayaran ?? '') == 'Lunas' ? 'bg-success' : (($h->pembayaran->status_pembayaran ?? '') == 'Belum Lunas' ? 'bg-danger' : 'bg-secondary') }} rounded-pill d-block mb-1">
                                        {{ $h->pembayaran->status_pembayaran ?? 'Belum Ditimbang' }}
                                    </span>
                                    <small class="text-muted fw-bold">{{ $h->total_berat ?? '?' }} {{ str_contains(strtolower($h->layanan->nama_layanan), 'satuan') ? 'Pcs' : 'Kg' }}</small>
                                </div>
                            </li>
                            @empty
                            <li class="list-group-item text-center py-4 text-muted">
                                <em>Belum ada transaksi hari ini.</em>
                            </li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>

        </div>
    </div>

</body>
</html>