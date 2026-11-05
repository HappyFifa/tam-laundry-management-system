<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\KurirController;
use App\Http\Controllers\PelangganController;

// ==========================================================
// RUTE GUEST (BELUM LOGIN)
// ==========================================================
Route::middleware(['guest'])->group(function () {
    Route::get('/login',              [AuthController::class, 'showLoginPelanggan'])->name('login');
    Route::post('/login',             [AuthController::class, 'loginCustomer'])->name('customer.login.submit');

    Route::get('/register-pelanggan',  [AuthController::class, 'showRegisterPelanggan'])->name('register.pelanggan');
    Route::post('/register-pelanggan', [AuthController::class, 'registerCustomer'])->name('customer.register.submit');

    Route::get('/lupa-password',       [AuthController::class, 'showLupaPassword'])->name('lupa.password');
    Route::post('/lupa-password',      [AuthController::class, 'lupaPassword'])->name('lupa.password.submit');

    Route::get('/login-pegawai',       [AuthController::class, 'showLoginForm'])->name('login.pegawai');
    Route::post('/login-pegawai',      [AuthController::class, 'login'])->name('pegawai.login.submit');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ==========================================================
// RUTE HALAMAN UTAMA / REDIRECT ROLE
// ==========================================================
Route::get('/', function () {
    if (auth()->check()) {
        $role = strtolower(auth()->user()->role);
        if ($role === 'admin') return redirect('/admin/dashboard');
        if ($role === 'kurir') return redirect('/kurir/dashboard');
        return view('pelanggan.form-pesan'); 
    }
    return view('landing'); 
})->name('home');

// ==========================================================
// RUTE PELANGGAN (WAJIB LOGIN)
// ==========================================================
Route::middleware(['auth'])->group(function () {
    Route::post('/simpan-pesanan',         [TransactionController::class, 'simpanPesanan'])->name('simpan.pesanan');
    Route::get('/pelanggan/dashboard',     [PelangganController::class, 'dashboard'])->name('customer.dashboard');
    Route::post('/pelanggan/batal/{resi}', [TransactionController::class, 'batalkanPesanan']); // 👉 ROUTE BATAL PESANAN
});

// ==========================================================
// RUTE ADMIN & KURIR (WAJIB LOGIN)
// ==========================================================
Route::middleware(['auth'])->group(function () {
    // --- RUTE ADMIN ---
    Route::get('/admin/dashboard',                   [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::post('/admin/update-status/{no_resi}',    [AdminController::class, 'updateStatus']);
    Route::post('/admin/input-berat/{no_resi}',      [AdminController::class, 'inputBeratAdmin']);
    Route::post('/admin/konfirmasi-lunas/{no_resi}', [AdminController::class, 'konfirmasiLunas']);
    Route::get('/admin/export-csv',                  [AdminController::class, 'exportCSV'])->name('admin.export');

    // 🔥 RUTE HAPUS PESANAN (BERSIH-BERSIH DATA TEST) 🔥
    Route::get('/admin/hapus-pesanan/{resi}',        [AdminController::class, 'hapusPesanan'])->name('admin.hapus.pesanan');

    // --- RUTE KURIR ---
    Route::get('/kurir/dashboard',                   [KurirController::class, 'dashboard'])->name('kurir.dashboard');
    Route::post('/kurir/konfirmasi-jemput/{resi}',   [KurirController::class, 'konfirmasiJemput'])->name('kurir.konfirmasiJemput');
    Route::post('/kurir/selesai-antar/{resi}',       [KurirController::class, 'konfirmasiJemput'])->name('kurir.selesaiAntar');
});

// ==========================================================
// RUTE PUBLIK / STRUK & PEMBAYARAN
// ==========================================================
Route::get('/struk/{resi}',        [TransactionController::class, 'halamanStruk'])->name('struk');

// 🔥 RUTE BARU UNTUK KLIK BAYAR DUITKU 🔥
Route::get('/bayar-duitku/{resi}', [AdminController::class, 'redirectDuitku'])->name('bayar.duitku');

// 🔥 RUTE WEBHOOK DUITKU 🔥
Route::post('/duitku/callback',    [AdminController::class, 'callbackDuitku']);