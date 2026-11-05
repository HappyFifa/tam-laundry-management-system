<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $table = 'transactions';
    
    protected $fillable = [
        'user_id', 'layanan_id', 'no_resi', 'metode_kirim', 'metode_pembayaran', 'alamat', 
        'latitude', 'longitude', 'total_berat', 'nama_kurir', 'catatan', 
        'ewp_menit', 'status_pengerjaan', 'waktu_mulai_cuci', 'foto_jemput', 'foto_antar', 'waktu_selesai_cuci'
    ];

    public function user() { return $this->belongsTo(User::class, 'user_id'); }
    public function layanan() { return $this->belongsTo(Layanan::class, 'layanan_id'); }
    public function pembayaran() { return $this->hasOne(Pembayaran::class, 'transaksi_id'); }
}