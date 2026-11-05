<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // ==========================================
    // LOGIN PEGAWAI INTERNAL (Admin & Kurir)
    // ==========================================
    public function showLoginForm()
    {
        return view('login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            
            // Ubah string ke huruf kecil semua agar aman dari perbedaan kapital
            $role = strtolower(Auth::user()->role); 
            
            // Paksa arahkan menggunakan redirect() biasa, jangan pakai intended()
            if ($role == 'admin') {
                return redirect('/admin/dashboard'); 
            } elseif ($role == 'kurir') {
                return redirect('/kurir/dashboard');
            } else {
                return redirect('/pelanggan/dashboard');
            }
        }

        // [REVISI 4] MENGEMBALIKAN PESAN ERROR JIKA EMAIL/PASSWORD PEGAWAI SALAH
        return back()->with('error', 'Login Gagal! Email atau Kata Sandi Anda salah.');
    }

    // ==========================================
    // TAMPILAN HALAMAN PELANGGAN
    // ==========================================
    public function showLoginPelanggan()
    {
        return view('auth.login-pelanggan');
    }

    public function showRegisterPelanggan()
    {
        return view('auth.register-pelanggan');
    }

    public function showLupaPassword()
    {
        return view('auth.lupa-password');
    }

    // ==========================================
    // REGISTRASI PELANGGAN BARU
    // ==========================================
    public function registerCustomer(Request $request)
    {
        // [REVISI 1] VALIDASI WA: WAJIB ANGKA & MAKSIMAL 12 DIGIT
        $request->validate([
            'nama'     => 'required|string|max:255',
            'no_wa'    => 'required|numeric|digits_between:10,12|unique:users,no_wa',
            'password' => 'required|string|min:6',
        ], [
            'nama.required'     => '⚠️ Nama wajib diisi!',
            'no_wa.required'    => '⚠️ Nomor WhatsApp aktif wajib diisi!',
            'no_wa.numeric'     => '⚠️ Pendaftaran Gagal: Nomor WhatsApp hanya boleh berisi angka!',
            'no_wa.digits_between' => '⚠️ Pendaftaran Gagal: Nomor WhatsApp harus 10 hingga 12 angka!',
            'no_wa.unique'      => '⚠️ Pendaftaran Gagal: Nomor WhatsApp ini sudah terdaftar.',
            'password.required' => '⚠️ Kata sandi wajib diisi!',
            'password.min'      => '⚠️ Kata sandi minimal 6 karakter!',
        ]);

        // Simpan akun pelanggan baru
        $user = User::create([
            'name'     => $request->nama,
            'no_wa'    => $request->no_wa,
            'email'    => $request->no_wa . '@tam-laundry.com',
            'password' => Hash::make($request->password),
            'role'     => 'Pelanggan',
        ]);

        // Kirim kredensial otomatis ke WhatsApp pelanggan
        $pesan = "Halo *{$user->name}*!\n\n"
               . "Selamat datang di *T.A.M Laundry* 🫧\n"
               . "Registrasi akun Anda berhasil!\n\n"
               . "📋 *Detail Login Akun:*\n"
               . "• Username (No WA): *{$user->no_wa}*\n"
               . "• Password: *{$request->password}*\n\n"
               . "Simpan pesan ini baik-baik. Gunakan akun ini untuk memesan laundry dan memantau status cucian Anda secara real-time.\n\n"
               . "🌐 Akses: " . url('/login');

        $this->kirimWhatsApp($user->no_wa, $pesan);

        return redirect()->route('login')
                         ->with('sukses', 'Akun berhasil dibuat! Detail login telah dikirim ke WhatsApp Anda.');
    }

    // ==========================================
    // LOGIN PELANGGAN (No WA + Password)
    // ==========================================
    public function loginCustomer(Request $request)
    {
        // [REVISI 2] VALIDASI WA: WAJIB ANGKA & MAKSIMAL 12 DIGIT
        $request->validate([
            'no_wa'    => 'required|numeric|digits_between:10,12',
            'password' => 'required',
        ], [
            'no_wa.required'    => '⚠️ Nomor WhatsApp wajib diisi!',
            'no_wa.numeric'     => '⚠️ Login Gagal: Format nomor salah! Harus berupa angka.',
            'no_wa.digits_between' => '⚠️ Login Gagal: Format nomor salah! Maksimal 12 angka.',
            'password.required' => '⚠️ Kata sandi wajib diisi!',
        ]);

        $user = User::where('no_wa', $request->no_wa)
                    ->where('role', 'Pelanggan')
                    ->first();

        if ($user && Hash::check($request->password, $user->password)) {
            Auth::login($user);
            $request->session()->regenerate();
            return redirect()->route('customer.dashboard')
                             ->with('sukses', 'Selamat datang, ' . $user->name . '!');
        }

        return back()->with('error', 'Login Gagal! Nomor WhatsApp atau kata sandi Anda salah.');
    }

    // ==========================================
    // LUPA PASSWORD — KIRIM ULANG VIA WHATSAPP
    // ==========================================
    public function lupaPassword(Request $request)
    {
        $request->validate([
            'no_wa' => 'required',
        ], [
            'no_wa.required' => '⚠️ Nomor WhatsApp wajib diisi!',
        ]);

        $user = User::where('no_wa', $request->no_wa)
                    ->where('role', 'Pelanggan')
                    ->first();

        if (!$user) {
            return back()->with('error', 'Nomor WhatsApp tidak terdaftar di sistem kami.');
        }

        // Generate password baru
        $passwordBaru = 'TAM' . rand(1000, 9999);

        // Update password di database
        $user->update([
            'password' => Hash::make($passwordBaru),
        ]);

        // Kirim password baru ke WhatsApp
        $pesan = "Halo *{$user->name}*!\n\n"
               . "Permintaan reset kata sandi T.A.M Laundry diterima.\n\n"
               . "🔑 *Kata Sandi Baru Anda:*\n"
               . "• No WA: *{$user->no_wa}*\n"
               . "• Password Baru: *{$passwordBaru}*\n\n"
               . "Segera ganti kata sandi setelah berhasil masuk.\n"
               . "🌐 Login: " . url('/login');

        $this->kirimWhatsApp($user->no_wa, $pesan);

        return redirect()->route('login')
                         ->with('sukses', 'Kata sandi baru telah dikirim ke WhatsApp Anda!');
    }

    // ==========================================
    // LOGOUT
    // ==========================================
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }

    // ==========================================
    // HELPER: KIRIM WHATSAPP VIA FONNTE API
    // ==========================================
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
            // Gagal kirim WA tidak menghentikan proses utama
        }
    }
}