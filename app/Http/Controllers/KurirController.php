<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use Illuminate\Support\Facades\File;

class KurirController extends Controller
{
    // ==========================================
    // MENAMPILKAN HALAMAN DASBOR KURIR
    // ==========================================
    public function dashboard()
    {
        $transaksi = Transaction::with(['user', 'layanan'])
            ->whereIn('status_pengerjaan', ['Menunggu Penjemputan', 'Siap Diantar', 'Sedang Diantar'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('kurir.dashboard', compact('transaksi'));
    }

    // ==========================================
    // FUNGSI KONFIRMASI PENJEMPUTAN / PENGANTARAN
    // ==========================================
    public function konfirmasiJemput(Request $request, $resi)
    {
        // 1. Validasi File Upload Foto Bukti
        $request->validate([
            'foto_bukti' => 'required|image|mimes:jpeg,png,jpg|max:5120', // Maks 5MB
        ]);

        $transaksi = Transaction::with('user')->where('no_resi', $resi)->first();

        if (!$transaksi) {
            return back()->with('error', 'Data pesanan tidak ditemukan!');
        }

        // Simpan File Foto ke public/uploads/bukti_kurir
        $file = $request->file('foto_bukti');
        $nama_file = time() . '_' . $resi . '_' . $file->getClientOriginalName();
        $file->move(public_path('uploads/bukti_kurir'), $nama_file);

        // 3. Logika Update Status & Simpan Foto
        if ($transaksi->status_pengerjaan == 'Menunggu Penjemputan') {
            
            // Simpan foto penjemputan
            $transaksi->update([
                'status_pengerjaan' => 'Menunggu Timbangan',
                'foto_jemput'       => $nama_file
            ]);

            // PERUBAHAN: KIRIM WHATSAPP SAAT DIJEMPUT (Revisi No 1)
            if ($transaksi->user && $transaksi->user->no_wa) {
                $pesan = "Halo *{$transaksi->user->name}*!\n\n"
                       . "🛵 Pakaian Anda (*#{$transaksi->no_resi}*) telah *BERHASIL DIJEMPUT* oleh Kurir kami beserta bukti foto.\n\n"
                       . "Pakaian sedang dibawa ke gerai. Mohon menunggu proses timbangan oleh Admin kami.\n\n"
                       . "🌐 Pantau status di: " . url('/login');
                $this->kirimWhatsApp($transaksi->user->no_wa, $pesan);
            }

            return back()->with('sukses', 'Pesanan berhasil dijemput & Bukti foto tersimpan! Status: Menunggu Timbangan.');

        } elseif (in_array($transaksi->status_pengerjaan, ['Siap Diantar', 'Sedang Diantar'])) {
            
            // Simpan foto pengantaran
            $transaksi->update([
                'status_pengerjaan'  => 'Selesai',
                'waktu_selesai_cuci' => now(),
                'foto_antar'         => $nama_file
            ]);

            // KIRIM WHATSAPP SELESAI DIANTAR
            if ($transaksi->user && $transaksi->user->no_wa) {
                $pesan = "Halo *{$transaksi->user->name}*!\n\n"
                       . "🫧 Pesanan T.A.M Laundry Anda (*#{$transaksi->no_resi}*) telah *SELESAI DIANTAR* dan sampai di alamat tujuan beserta bukti foto penerimaan.\n\n"
                       . "Terima kasih telah mempercayakan pakaian bersih & wangi Anda kepada *T.A.M Laundry*! 🙏\n\n"
                       . "🌐 Cek riwayat: " . url('/login');
                $this->kirimWhatsApp($transaksi->user->no_wa, $pesan);
            }

            return back()->with('sukses', 'Pesanan berhasil diantar ke pelanggan & Bukti foto tersimpan! Status: Selesai.');
        }

        return back()->with('error', 'Status pesanan tidak valid untuk aksi kurir saat ini.');
    }

    // ==========================================================
    // HELPER: KIRIM WHATSAPP VIA FONNTE API
    // ==========================================================
    private function kirimWhatsApp($target, $pesan)
    {
        try {
            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL            => 'https://api.fonnte.com/send',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_MAXREDIRS      => 10,
                CURLOPT_TIMEOUT        => 15,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST  => 'POST',
                CURLOPT_POSTFIELDS     => [
                    'target'      => $target,
                    'message'     => $pesan,
                    'countryCode' => '62',
                ],
                CURLOPT_HTTPHEADER => [
                    'Authorization: ' . env('FONNTE_TOKEN', ''),
                ],
            ]);
            curl_exec($curl);
            curl_close($curl);
        } catch (\Exception $e) {
        }
    }
}