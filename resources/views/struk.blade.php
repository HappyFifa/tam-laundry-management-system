<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Pesanan - T.A.M Laundry</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f4f7f6; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .struk-card { border-radius: 16px; max-width: 480px; margin: 0 auto; }
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; }
            .struk-card { box-shadow: none !important; border: none !important; }
        }
    </style>
</head>
<body class="pt-4 pb-5">

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

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">

                @if(session('sukses'))
                    <div class="alert alert-success fw-bold text-center shadow-sm no-print mb-3 rounded-pill">
                        🎉 {{ session('sukses') }}
                    </div>
                @endif
                
                {{-- TAMBAHAN: PESAN ERROR DUITKU/SISTEM --}}
                @if(session('error'))
                    <div class="alert alert-danger fw-bold text-center shadow-sm no-print mb-3 rounded-pill">
                        ❌ {{ session('error') }}
                    </div>
                @endif

                <div class="card struk-card shadow-sm border-0">
                    <div class="card-body p-4 p-sm-5">
                        <div class="text-center mb-4">
                            <h4 class="fw-bold mb-0">🫧 T.A.M LAUNDRY</h4>
                            <small class="text-muted">Jl. Awibitung Gg. Jembar 4 No.4, Cicadas, Bandung</small>
                        </div>

                        <div class="d-flex justify-content-between border-bottom pb-3 mb-3">
                            <div>
                                <small class="text-muted d-block">No. Resi</small>
                                <strong class="fs-5">{{ $transaksi->no_resi }}</strong>
                            </div>
                            <div class="text-end">
                                <small class="text-muted d-block">Tanggal Pemesanan</small>
                                <strong class="small">{{ \Carbon\Carbon::parse($transaksi->created_at)->format('d M Y, H:i') }}</strong>
                            </div>
                        </div>

                        <table class="table table-borderless mb-0 small">
                            <tr>
                                <td class="text-muted ps-0 py-1">Pelanggan</td>
                                <td class="text-end pe-0 fw-bold py-1">{{ $transaksi->user->name }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted ps-0 py-1">WhatsApp</td>
                                <td class="text-end pe-0 py-1">{{ $transaksi->user->no_wa }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted ps-0 py-1">Layanan</td>
                                <td class="text-end pe-0 py-1">{{ $transaksi->layanan->nama_layanan }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted ps-0 py-1">Metode Bayar</td>
                                <td class="text-end pe-0 fw-bold py-1 text-primary">{{ $transaksi->metode_pembayaran }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted ps-0 py-1">Metode Kirim</td>
                                <td class="text-end pe-0 py-1">{{ $transaksi->metode_kirim }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted ps-0 py-1">Kurir</td>
                                <td class="text-end pe-0 py-1 fw-bold">
                                    @if($transaksi->metode_kirim == 'Bawa Sendiri')
                                        <span class="text-secondary">-</span>
                                    @else
                                        {{ $transaksi->nama_kurir ?? 'Belum ditentukan' }}
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted ps-0 py-1">Berat/Jumlah</td>
                                <td class="text-end pe-0 py-1">
                                    @if($transaksi->total_berat)
                                        {{ $transaksi->total_berat }} {{ str_contains(strtolower($transaksi->layanan->nama_layanan), 'satuan') ? 'Pcs' : 'Kg' }}
                                    @else
                                        Menunggu timbangan admin
                                    @endif
                                </td>
                            </tr>
                            
                            @if($transaksi->catatan)
                            <tr>
                                <td class="text-muted ps-0 py-1">Catatan Khusus</td>
                                <td class="text-end pe-0 py-1 text-danger fw-bold fst-italic">"{{ $transaksi->catatan }}"</td>
                            </tr>
                            @endif

                            <tr>
                                <td class="text-muted ps-0 py-1">Waktu Pengerjaan (EWP)</td>
                                <td class="text-end pe-0 py-1 fw-bold text-primary">
                                    @if($transaksi->ewp_menit > 0)
                                        ⏱️ {{ formatEWP($transaksi->ewp_menit) }}
                                    @else
                                        Menunggu Timbangan
                                    @endif
                                </td>
                            </tr>

                            <tr>
                                <td class="text-muted ps-0 py-1">Jadwal Pengambilan/Pengantaran</td>
                                <td class="text-end pe-0 py-1 fw-bold text-info">
                                    @if($transaksi->ewp_menit > 0 && $transaksi->status_pengerjaan != 'Selesai' && $transaksi->status_pengerjaan != 'Dibatalkan')
                                        @if($transaksi->waktu_selesai_cuci)
                                            📅 {{ \Carbon\Carbon::parse($transaksi->waktu_selesai_cuci)->translatedFormat('l, d F Y - H:i') }} WIB
                                        @endif
                                    @elseif($transaksi->status_pengerjaan == 'Selesai')
                                        <span class="text-success">✅ Selesai Dikerjakan</span>
                                    @else
                                        Menunggu Timbangan
                                    @endif
                                </td>
                            </tr>
                            
                            @if($transaksi->waktu_mulai_cuci)
                            <tr>
                                <td class="text-muted ps-0 py-1">Mulai Masuk Mesin</td>
                                <td class="text-end pe-0 py-1 text-secondary">{{ \Carbon\Carbon::parse($transaksi->waktu_mulai_cuci)->translatedFormat('d M Y, H:i') }} WIB</td>
                            </tr>
                            @endif

                            @if($transaksi->status_pengerjaan == 'Selesai' && $transaksi->waktu_selesai_cuci)
                            <tr>
                                <td class="text-muted ps-0 py-1">Selesai Diproses (Aktual)</td>
                                <td class="text-end pe-0 py-1 text-success fw-bold">{{ \Carbon\Carbon::parse($transaksi->waktu_selesai_cuci)->translatedFormat('d M Y, H:i') }} WIB</td>
                            </tr>
                            @endif
                        </table>

                        @if($transaksi->status_pengerjaan == 'Selesai' && $transaksi->waktu_mulai_cuci && $transaksi->waktu_selesai_cuci)
                            @php
                                $mulai = \Carbon\Carbon::parse($transaksi->waktu_mulai_cuci);
                                $selesai = \Carbon\Carbon::parse($transaksi->waktu_selesai_cuci);
                                $durasi_aktual_menit = $mulai->diffInMinutes($selesai);
                            @endphp
                            
                            <div class="alert bg-light border border-secondary border-opacity-25 rounded-3 py-2 px-3 mt-3 mb-0 text-center">
                                <span class="d-block text-muted small mb-1">Durasi Pengerjaan Aktual:</span>
                                <h6 class="fw-bold text-dark mb-2">⏱️ {{ formatEWP($durasi_aktual_menit) }}</h6>
                                
                                @if($durasi_aktual_menit <= $transaksi->ewp_menit)
                                    <span class="badge bg-success rounded-pill px-3 py-2 w-100 shadow-sm">
                                        ✨ Selesai Tepat Waktu / Lebih Cepat!
                                    </span>
                                @else
                                    <span class="badge bg-danger rounded-pill px-3 py-2 w-100 shadow-sm">
                                        ⚠️ Terlambat dari Estimasi Sistem
                                    </span>
                                @endif
                            </div>
                        @endif

                        <div class="border-top border-bottom py-3 my-3 d-flex justify-content-between align-items-center">
                            <h5 class="mb-0 text-muted">Total Tagihan</h5>
                            <h4 class="mb-0 fw-bold">
                                @if($transaksi->pembayaran && $transaksi->pembayaran->total_harga > 0)
                                    Rp {{ number_format($transaksi->pembayaran->total_harga, 0, ',', '.') }}
                                @else
                                    <span class="fs-6 text-muted">Belum dihitung</span>
                                @endif
                            </h4>
                        </div>

                        <div class="text-center mt-3">
                            @if(!$transaksi->pembayaran)
                                <div class="alert alert-secondary py-2 rounded-pill border-0">
                                    <small>⏳ Menunggu admin menimbang pakaian Anda...</small>
                                </div>
                            @elseif($transaksi->pembayaran->status_pembayaran == 'Belum Lunas')
                                <h6 class="text-danger fw-bold mb-3">🔴 STATUS: BELUM DIBAYAR</h6>
                                
                                @if($transaksi->metode_pembayaran == 'Online')
                                    @if($transaksi->pembayaran->snap_token)
                                        <div class="alert alert-warning small py-2 mb-3 text-center border-0 fw-bold text-dark rounded-3">
                                            ⏳ Segera bayar. Tagihan ini otomatis hangus dalam 24 Jam.
                                        </div>
                                        
                                        {{-- 🔴 LOGIKA BARU: FORM DROPDOWN OVO & BNI --}}
                                        <form action="{{ route('bayar.duitku', $transaksi->no_resi) }}" method="GET" class="no-print mt-3">
                                            <div class="mb-3 text-start px-2">
                                                <label class="small fw-bold text-dark mb-2">Pilih Metode Pembayaran Daring:</label>
                                                <select name="metode" class="form-select border-primary shadow-sm mb-3" required>
                                                    <option value="I1">🏦 BNI Virtual Account</option>
                                                    <option value="OV">🟣 OVO E-Wallet</option>
                                                </select>
                                            </div>
                                            <button type="submit" class="btn btn-primary btn-lg w-100 fw-bold rounded-pill shadow-sm">
                                                💳 Lanjutkan Pembayaran
                                            </button>
                                        </form>

                                    @else
                                        <p class="text-danger small fw-bold">Gagal memuat API Pembayaran. Silakan hubungi admin.</p>
                                    @endif
                                @else
                                    <div class="alert alert-warning py-3 rounded-4 border-0 mb-0">
                                        <h6 class="fw-bold text-dark mb-1">💵 Metode Bayar: TUNAI</h6>
                                        <small class="text-dark">Silakan serahkan uang tunai Anda ke Kurir atau Admin di Outlet saat penyerahan pakaian.</small>
                                    </div>
                                @endif

                            @elseif($transaksi->pembayaran->status_pembayaran == 'Lunas')
                                <h5 class="text-success fw-bold">✅ LUNAS TERIMA KASIH!</h5>
                                <p class="text-muted small mb-0">Terima kasih telah mempercayakan cucian Anda pada kami.</p>
                            @endif
                        </div>

                    </div>
                </div>

                <div class="d-flex justify-content-between mt-3 gap-2 no-print">
                    <a href="{{ route('customer.dashboard') }}" class="btn btn-outline-secondary rounded-pill fw-bold flex-fill">
                        ← Kembali ke Dasbor
                    </a>
                    <button onclick="window.print()" class="btn btn-dark fw-bold rounded-pill flex-fill shadow-sm">
                        🖨️ Cetak / PDF
                    </button>
                </div>

            </div>
        </div>
    </div>
</body>
</html>