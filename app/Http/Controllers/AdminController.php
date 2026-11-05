<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\Pembayaran;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class AdminController extends Controller
{
    public function dashboard(Request $request)
    {
        $query = Transaction::with(['user', 'layanan', 'pembayaran'])->orderBy('created_at', 'desc');

        if ($request->has('start_date') && $request->has('end_date') && $request->start_date != '' && $request->end_date != '') {
            $start = $request->start_date . ' 00:00:00';
            $end = $request->end_date . ' 23:59:59';
            $query->whereBetween('created_at', [$start, $end]);
        }

        $transaksi = $query->get();
        $transaksi_ids = $transaksi->pluck('id');
        
        $total_pendapatan = Pembayaran::whereIn('transaksi_id', $transaksi_ids)
                                      ->where('status_pembayaran', 'Lunas')
                                      ->sum('total_harga');

        $pesanan_aktif = Transaction::whereIn('status_pengerjaan', ['Menunggu Penjemputan', 'Menunggu Timbangan', 'Sedang Dicuci', 'Sedang Diantar'])->count();
        $laporan_hari_ini = Transaction::with(['user', 'layanan', 'pembayaran'])->whereDate('created_at', Carbon::today())->orderBy('created_at', 'desc')->get();
        
        return view('admin.dashboard', compact('transaksi', 'total_pendapatan', 'pesanan_aktif', 'laporan_hari_ini'));
    }

    public function updateStatus(Request $request, $no_resi)
    {
        $transaksi = Transaction::with(['user', 'pembayaran'])->where('no_resi', $no_resi)->first();
        if($transaksi) {
            if ($request->status == 'Selesai') {
                if (!$transaksi->pembayaran || $transaksi->pembayaran->status_pembayaran != 'Lunas') {
                    return back()->with('error', '⚠️ GAGAL! Pesanan belum LUNAS. Pastikan pembayaran telah diterima sebelum menutup pesanan.');
                }
                $transaksi->waktu_selesai_cuci = now();
            }

            $transaksi->status_pengerjaan = $request->status;
            $transaksi->save();

            if ($transaksi->user && $transaksi->user->no_wa) {
                $status_teks = $request->status;
                if($request->status == 'Siap Diantar') {
                    $status_teks = "Siap Diantar (Kurir sedang bersiap menuju lokasi Anda)";
                }

                $pesan = "Halo *{$transaksi->user->name}*!\n\n"
                       . "Cucian Anda No. Resi *{$no_resi}* diperbarui.\n"
                       . "Status Saat Ini: *{$status_teks}* ✅\n\n"
                       . "Pantau di: " . url('/lacak');
                $this->kirimWhatsApp($transaksi->user->no_wa, $pesan);
            }
        }
        return back()->with('sukses', 'Status pesanan berhasil diubah!');
    }

    public function konfirmasiLunas($no_resi)
    {
        $transaksi = Transaction::with('pembayaran')->where('no_resi', $no_resi)->first();
        if ($transaksi && $transaksi->pembayaran) {
            $transaksi->pembayaran->update(['status_pembayaran' => 'Lunas']);
            return back()->with('sukses', 'Uang tunai diterima! Pesanan berhasil ditandai LUNAS.');
        }
        return back()->with('error', 'Data pesanan tidak valid.');
    }

    private function faktorial($n) { return ($n <= 1) ? 1 : $n * $this->faktorial($n - 1); }

    private function hitungJarak($lat1, $lon1, $lat2, $lon2) {
        if (($lat1 == $lat2) && ($lon1 == $lon2)) return 0;
        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        return $dist * 60 * 1.1515 * 1.609344;
    }

    public function inputBeratAdmin(Request $request, $no_resi)
    {
        $request->validate(['berat' => 'required|numeric|min:0.1']);
        $transaksi = Transaction::with(['user', 'layanan'])->where('no_resi', $no_resi)->first();
        if (!$transaksi) return back()->with('error', 'Data tidak ditemukan!');

        $jumlah_siklus = ceil($request->berat / 10); 
        $s = 2; 
        $lambda = Transaction::whereIn('status_pengerjaan', ['Menunggu Penjemputan', 'Menunggu Timbangan', 'Sedang Dicuci'])->count() + 1;
        $mu = 0.25; 
        $rho = $lambda / ($s * $mu);

        if ($rho >= 1) {
            $ewp_mesin_menit = ($jumlah_siklus * 240) + ($request->berat * 15);
        } else {
            $komponen_sum = 0;
            for ($n = 0; $n < $s; $n++) $komponen_sum += (pow(($lambda / $mu), $n) / $this->faktorial($n));
            $komponen_sum += (pow(($lambda / $mu), $s) / ($this->faktorial($s) * (1 - $rho)));
            $P0 = 1 / $komponen_sum;
            $Lq = (pow(($lambda / $mu), $s) * $lambda * $mu * $P0) / ($this->faktorial($s - 1) * pow(($s * $mu) - $lambda, 2));
            
            $Wq = $Lq / $lambda; 
            $W = $Wq + ($jumlah_siklus * 4); 
            $ewp_mesin_menit = round($W * 60) + round($request->berat * 5);
        }

        $nama_layanan = $transaksi->layanan->nama_layanan;
        if ($nama_layanan == 'Cuci Satuan (Karpet)') {
            $ewp_final = 4320;
        } elseif (in_array($nama_layanan, ['Cuci Satuan (Selimut)', 'Cuci Satuan (Mukena)'])) {
            $ewp_final = 1440;
        } elseif ($nama_layanan == 'Cuci Setrika') {
            $antreanSetrika = Transaction::whereHas('layanan', function($q) { $q->where('nama_layanan', 'Cuci Setrika'); })
                ->whereIn('status_pengerjaan', ['Menunggu Penjemputan', 'Menunggu Timbangan', 'Sedang Dicuci', 'Sedang Diantar'])
                ->where('id', '<', $transaksi->id)->count();
            $ewp_final = $ewp_mesin_menit + ($antreanSetrika * 1440) + 1440; 
        } else {
            $ewp_final = $ewp_mesin_menit; 
        }

        $jadwal_diambil = Carbon::now();
        
        if ($jadwal_diambil->format('H:i') < '08:00') {
            $jadwal_diambil->setTime(8, 0, 0);
        } 
        elseif ($jadwal_diambil->format('H:i') >= '19:00') {
            $jadwal_diambil->addDay()->setTime(8, 0, 0);
        }

        $sisa_menit = $ewp_final;

        while ($sisa_menit > 0) {
            $tutup_hari_ini = $jadwal_diambil->copy()->setTime(19, 0, 0);
            $menit_tersedia_hari_ini = $jadwal_diambil->diffInMinutes($tutup_hari_ini);

            if ($sisa_menit <= $menit_tersedia_hari_ini) {
                $jadwal_diambil->addMinutes($sisa_menit);
                $sisa_menit = 0;
            } else {
                $sisa_menit -= $menit_tersedia_hari_ini;
                $jadwal_diambil->addDay()->setTime(8, 0, 0);
            }
        }

        if ($transaksi->layanan->harga_satuan) {
            $harga_layanan = $request->berat * $transaksi->layanan->harga_satuan;
        } else {
            $harga_layanan = $request->berat * $transaksi->layanan->harga_per_kg;
        }

        $ongkir = 0;
        if ($transaksi->metode_kirim == 'Antar Jemput') {
            if ($transaksi->latitude && $transaksi->longitude) {
                $jarak_km = $this->hitungJarak(-6.908795, 107.639089, $transaksi->latitude, $transaksi->longitude);
                $multiplier = ceil($jarak_km / 10);
                if ($multiplier < 1) $multiplier = 1; 
                $ongkir = 7500 * $multiplier;
            } else {
                $ongkir = 7500;
            }
        }

        $total_harga = $harga_layanan + $ongkir;
        $statusToken = ($transaksi->metode_pembayaran == 'Online') ? 'READY' : null;

        $transaksi->update([
            'total_berat' => $request->berat,
            'ewp_menit' => $ewp_final, 
            'waktu_selesai_cuci' => $jadwal_diambil, 
            'status_pengerjaan' => 'Sedang Dicuci',
            'waktu_mulai_cuci' => now(),
        ]);

        Pembayaran::updateOrCreate(
            ['transaksi_id' => $transaksi->id],
            ['total_harga' => $total_harga, 'snap_token' => $statusToken, 'status_pembayaran' => 'Belum Lunas']
        );

        if ($transaksi->user && $transaksi->user->no_wa) {
            $hari_teks = floor($ewp_final / 1440);
            $sisa_jam = $ewp_final % 1440;
            $jam_teks = floor($sisa_jam / 60);
            $menit_teks = $sisa_jam % 60;
            
            $str_ewp = "";
            if ($hari_teks > 0) $str_ewp .= "$hari_teks Hari ";
            if ($jam_teks > 0) $str_ewp .= "$jam_teks Jam ";
            if ($menit_teks > 0) $str_ewp .= "$menit_teks Menit";
            $str_ewp = trim($str_ewp);

            Carbon::setLocale('id'); 
            $format_jadwal = $jadwal_diambil->translatedFormat('l, d F Y - H:i') . " WIB";

            $pesan = "Halo *{$transaksi->user->name}*!\n\n"
                   . "Pakaian Anda No. Resi *{$no_resi}* telah ditimbang (*{$request->berat} Kg/Pcs*).\n"
                   . "Status: *Sedang Dicuci* 🔄\n\n"
                   . "⏱️ Waktu Pengerjaan: *{$str_ewp}*\n"
                   . "📅 Jadwal Diambil/Diantar: *{$format_jadwal}*\n\n"
                   . "Biaya Cucian: Rp " . number_format($harga_layanan, 0, ',', '.') . "\n"
                   . "Biaya Antar-Jemput: Rp " . number_format($ongkir, 0, ',', '.') . "\n"
                   . "Total Tagihan: *Rp " . number_format($total_harga, 0, ',', '.') . "*\n\n"
                   . "Silakan login untuk melunasi tagihan Anda.\n"
                   . "Pantau di: " . url('/lacak');
            
            $this->kirimWhatsApp($transaksi->user->no_wa, $pesan);
        }

        return back()->with('sukses', "Validasi berhasil! Pesanan siap dicuci.");
    }

    public function redirectDuitku($no_resi)
    {
        $transaksi = Transaction::with(['user', 'pembayaran', 'layanan'])->where('no_resi', $no_resi)->first();
        if (!$transaksi || !$transaksi->pembayaran) {
            return back()->with('error', 'Tagihan belum tersedia.');
        }

        // TANGKAP METODE DARI DROPDOWN STRUK (Default ke BNI VA 'I1' jika error)
        $metodeDipilih = request('metode', 'I1');

        // KUNCI DUITKU ANDA
        $merchantCode = trim('D24147'); 
        $merchantKey = trim('8316e5ec5d5ebbe612bb5f3d3c625053'); 
        
        $baseUrl = 'https://passport.duitku.com/webapi';

        $orderId = trim($no_resi);
        $amount = (int) $transaksi->pembayaran->total_harga;
        
        $signature = md5($merchantCode . $orderId . $amount . $merchantKey);

        $params = [
            'merchantCode'    => $merchantCode,
            'paymentAmount'   => $amount,
            'paymentMethod'   => $metodeDipilih, // MENGGUNAKAN PILIHAN PELANGGAN (I1 atau OV)
            'merchantOrderId' => $orderId,
            'productDetails'  => trim('Pembayaran Laundry ' . $orderId),
            'email'           => $transaksi->user->email ?? 'pelanggan@tam-laundry.com',
            'phoneNumber'     => trim($transaksi->user->no_wa),
            'customerVaName'  => trim(substr($transaksi->user->name, 0, 20)),
            'callbackUrl'     => url('/duitku/callback'),
            'returnUrl'       => route('struk', ['resi' => $no_resi]),
            'signature'       => $signature,
            'expiryPeriod'    => 1440
        ];

        try {
            $response = Http::withHeaders([
                'Accept'       => 'application/json',
                'Content-Type' => 'application/json'
            ])->post($baseUrl . '/api/merchant/v2/inquiry', $params);

            $result = $response->json();

            // Karena kita sudah milih metode dengan pasti, fallback tidak diperlukan lagi
            if (isset($result['paymentUrl']) && !empty($result['paymentUrl'])) {
                return redirect($result['paymentUrl']);
            } else {
                $errorMsg = $result['Message'] ?? ($result['statusMessage'] ?? json_encode($result));
                return back()->with('error', '⚠️ Duitku menolak transaksi: ' . $errorMsg);
            }
        } catch (\Exception $e) {
            return back()->with('error', '⚠️ Koneksi ke server Duitku terputus: ' . $e->getMessage());
        }
    }

    public function exportCSV(Request $request)
    {
        $fileName = 'Laporan_TAM_Laundry.csv';
        $query = Pembayaran::with(['transaksi.user', 'transaksi.layanan'])->where('status_pembayaran', 'Lunas');

        if ($request->has('start_date') && $request->has('end_date') && $request->start_date != '' && $request->end_date != '') {
            $start = $request->start_date . ' 00:00:00';
            $end = $request->end_date . ' 23:59:59';
            $query->whereHas('transaksi', function($q) use ($start, $end) {
                $q->whereBetween('created_at', [$start, $end]);
            });
        }

        $pembayaran = $query->get();

        $headers = ["Content-type" => "text/csv", "Content-Disposition" => "attachment; filename=$fileName", "Pragma" => "no-cache", "Cache-Control" => "must-revalidate, post-check=0, pre-check=0", "Expires" => "0"];
        $columns = ['No Resi', 'Nama Pelanggan', 'No WhatsApp', 'Jenis Layanan', 'Total Harga', 'Tanggal Lunas'];

        $callback = function() use($pembayaran, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            foreach ($pembayaran as $p) {
                fputcsv($file, [
                    $p->transaksi->no_resi, 
                    $p->transaksi->user->name, 
                    $p->transaksi->user->no_wa, 
                    $p->transaksi->layanan->nama_layanan, 
                    'Rp ' . $p->total_harga,
                    $p->updated_at
                ]);
            }
            fclose($file);
        };
        return response()->stream($callback, 200, $headers);
    }

    public function callbackDuitku(Request $request)
    {
        $merchantCode = env('D24147');
        $merchantKey = env('8316e5ec5d5ebbe612bb5f3d3c625053');
        
        $amount = $request->amount;
        $merchantOrderId = $request->merchantOrderId;
        $signature = $request->signature;
        $resultCode = $request->resultCode;

        if (!empty($merchantCode) && !empty($amount) && !empty($merchantOrderId) && !empty($signature)) {
            $calcSignature = md5($merchantCode . $amount . $merchantOrderId . $merchantKey);
            
            if ($signature == $calcSignature) {
                if ($resultCode == '00') {
                    $parts = explode('-', $merchantOrderId);
                    $no_resi = (count($parts) >= 2) ? $parts[0] . '-' . $parts[1] : $merchantOrderId;

                    $transaksi = Transaction::where('no_resi', $no_resi)->first();
                    if ($transaksi && $transaksi->pembayaran) {
                        $transaksi->pembayaran->update(['status_pembayaran' => 'Lunas']);
                        
                        if ($transaksi->user && $transaksi->user->no_wa) {
                            $pesan = "Halo *{$transaksi->user->name}*!\n\n"
                                   . "Pembayaran Anda untuk cucian *#{$no_resi}* sebesar *Rp " . number_format($amount, 0, ',', '.') . "* telah BERHASIL kami terima (LUNAS) ✅.\n\n"
                                   . "Terima kasih atas kepercayaannya!";
                            $this->kirimWhatsApp($transaksi->user->no_wa, $pesan);
                        }
                    }
                }
                return response()->json(['status' => 'success'], 200);
            }
        }
        return response()->json(['status' => 'bad signature'], 400);
    }

    public function hapusPesanan($no_resi)
    {
        $transaksi = Transaction::where('no_resi', $no_resi)->first();
        if ($transaksi) {
            Pembayaran::where('transaksi_id', $transaksi->id)->delete();
            $transaksi->delete();
            return back()->with('sukses', "🗑️ Pesanan $no_resi berhasil dihapus bersih dari sistem!");
        }
        return back()->with('error', 'Pesanan tidak ditemukan.');
    }

    private function kirimWhatsApp($target, $pesan)
    {
        try {
            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL => 'https://api.fonnte.com/send',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => ['target' => $target, 'message' => $pesan, 'countryCode' => '62'],
                CURLOPT_HTTPHEADER => [
                    'Authorization: ' . env('FONNTE_TOKEN', ''),
                ],
            ]);
            curl_exec($curl);
            curl_close($curl);
        } catch (\Exception $e) {}
    }
}