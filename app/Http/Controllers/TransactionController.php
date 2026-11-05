<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\Layanan;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    public function simpanPesanan(Request $request)
    {
        $request->validate([
            'layanan' => 'required', 
            'metode_kirim' => 'required',
            'metode_pembayaran' => 'required'
        ]);

        $harga_mapping = [
            'Cuci Kering' => 5000, 'Cuci Setrika' => 7000, 'Setrika Saja' => 4000,
            'Cuci Satuan (Karpet)' => 50000, 'Cuci Satuan (Selimut)' => 20000, 'Cuci Satuan (Mukena)' => 10000,
        ];

        $is_satuan = str_contains($request->layanan, 'Satuan');
        $harga_per_kg = $is_satuan ? null : ($harga_mapping[$request->layanan] ?? 5000);
        $harga_satuan = $is_satuan ? ($harga_mapping[$request->layanan] ?? 10000) : null;
        
        $layanan = Layanan::firstOrCreate(
            ['nama_layanan' => $request->layanan],
            ['harga_per_kg' => $harga_per_kg, 'harga_satuan' => $harga_satuan]
        );

        $alamat_lengkap = null;
        $latitude = $request->input('lat_gps');
        $longitude = $request->input('lng_gps');

        if ($request->metode_kirim == 'Antar Jemput') {
            $request->validate(['provinsi' => 'required', 'kota' => 'required', 'kecamatan' => 'required', 'detail_alamat' => 'required']);
            $alamat_lengkap = $request->detail_alamat . ', Kec. ' . $request->kecamatan . ', ' . $request->kota . ', ' . $request->provinsi . ' ' . $request->kode_pos;

            if (empty($latitude) || empty($longitude)) {
                try {
                    $response = Http::withHeaders(['User-Agent' => 'TAM-Laundry'])->get('https://nominatim.openstreetmap.org/search', ['q' => $alamat_lengkap, 'format' => 'json', 'limit' => 1]);
                    if ($response->successful() && count($response->json()) > 0) {
                        $latitude = $response->json()[0]['lat'];
                        $longitude = $response->json()[0]['lon'];
                    }
                } catch (\Exception $e) {}
            }
        }

        $no_resi = 'TRX-' . strtoupper(substr(uniqid(), -5));
        $status_awal = ($request->metode_kirim == 'Antar Jemput') ? 'Menunggu Penjemputan' : 'Menunggu Timbangan';
        $kurir_aktif = User::where('role', 'Kurir')->inRandomOrder()->first();

        Transaction::create([
            'user_id' => Auth::id(),
            'layanan_id' => $layanan->id,
            'no_resi' => $no_resi,
            'metode_kirim' => $request->metode_kirim,
            'metode_pembayaran' => $request->metode_pembayaran,
            'catatan' => $request->catatan,
            'alamat' => $alamat_lengkap,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'nama_kurir' => $kurir_aktif ? $kurir_aktif->name : 'Kurir T.A.M',
            'ewp_menit' => 0,
            'status_pengerjaan' => $status_awal,
        ]);

        return redirect()->route('struk', ['resi' => $no_resi])->with('sukses', 'Pesanan berhasil dibuat! Menunggu admin menimbang pakaian Anda.');
    }

    // 👈 FUNGSI BARU: BATALKAN PESANAN
    public function batalkanPesanan($resi)
    {
        $transaksi = Transaction::where('no_resi', $resi)->where('user_id', Auth::id())->first();
        
        if ($transaksi && in_array($transaksi->status_pengerjaan, ['Menunggu Penjemputan', 'Menunggu Timbangan'])) {
            $transaksi->update(['status_pengerjaan' => 'Dibatalkan']);
            return back()->with('sukses', 'Pesanan ' . $resi . ' berhasil dibatalkan.');
        }

        return back()->with('error', '⚠️ Pesanan tidak dapat dibatalkan karena sudah diproses oleh Admin/Kurir.');
    }

    public function lacak() { return view('lacak'); }

    public function cariResi(Request $request)
    {
        $request->validate(['no_wa' => 'required', 'no_resi' => 'required']);
        $transaksi = Transaction::with(['user', 'layanan', 'pembayaran'])
            ->whereHas('user', function($q) use ($request) { $q->where('no_wa', $request->no_wa); })->where('no_resi', $request->no_resi)->first();
        return view('lacak', compact('transaksi'))->with('dicari', true);
    }

    public function halamanStruk($resi)
    {
        $transaksi = Transaction::with(['user', 'layanan', 'pembayaran'])->where('no_resi', $resi)->first();
        if(!$transaksi) abort(404);
        return view('struk', compact('transaksi'));
    }

    public function suksesBayar($resi)
    {
        $transaksi = Transaction::where('no_resi', $resi)->first();
        if($transaksi && $transaksi->pembayaran) { $transaksi->pembayaran()->update(['status_pembayaran' => 'Lunas']); }
        return redirect()->route('struk', ['resi' => $resi])->with('sukses', 'Pembayaran QRIS Berhasil!');
    }
}