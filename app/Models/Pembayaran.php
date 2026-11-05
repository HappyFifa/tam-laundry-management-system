<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model
{
    protected $table = 'pembayaran';
    protected $fillable = ['transaksi_id', 'total_harga', 'snap_token', 'status_pembayaran'];

    public function transaksi()
    {
        return $this->belongsTo(Transaction::class, 'transaksi_id');
    }
}