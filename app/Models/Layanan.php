<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Layanan extends Model
{
    protected $table = 'layanan';
    public $timestamps = false;
    // TAMBAHKAN KOLOM HARGA SATUAN DI SINI
    protected $fillable = ['nama_layanan', 'harga_per_kg', 'harga_satuan'];

    public function transaksi()
    {
        return $this->hasMany(Transaction::class, 'layanan_id');
    }
}