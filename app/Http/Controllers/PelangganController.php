<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;

class PelangganController extends Controller
{
    public function dashboard()
    {
        $riwayat_cucian = Transaction::with(['layanan', 'pembayaran'])
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        return view('pelanggan.dashboard', compact('riwayat_cucian'));
    }
}