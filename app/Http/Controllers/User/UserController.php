<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Mail\VerifikasiEmailUntukRegistrasiPengaduanMahasiswa;
use App\Models\Mahasiswa;
use App\Models\Pengaduan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function index()
    {
        // Menghitung jumlah pengaduan yang ada di table
        $pengaduan = Pengaduan::all()->count();

        // Arahkan ke file user/landing.blade.php
        return view('user.landing', ['pengaduan' => $pengaduan]);
    }

    public function login(Request $request)
    {
        // Pengecekan $request->username isinya email atau username
        if (filter_var($request->username, FILTER_VALIDATE_EMAIL)) {
            // jika isinya string email, cek email nya di table mahasiswa
            $email = Mahasiswa::where('email', $request->username)->first();

            // Pengecekan variable $email jika tidak ada di table mahasiswa
            if (!$email) {
                return redirect()->back()->with(['pesan' => 'Email tidak terdaftar']);
            }

            // jika email ada, langsung check password yang dikirim di form dan di table, hasilnya sama atau tidak
            $password = Hash::check($request->password, $email->password);

            // Pengecekan variable $password jika password tidak sama dengan yang dikirimkan
            if (!$password) {
                return redirect()->back()->with(['pesan' => 'Password tidak sesuai']);
            }

            // Jalankan fungsi auth jika berjasil melewati validasi di atas
            if (Auth::guard('mahasiswa')->attempt(['email' => $request->username, 'password' => $request->password])) {
                // Jika login berhasil
                return redirect()->back();
            } else {
                // Jika login gagal
                return redirect()->back()->with(['pesan' => 'Akun tidak terdaftar!']);
            }
        } else {
            // jika isinya string username, cek username nya di table mahasiswa
            $username = Mahasiswa::where('username', $request->username)->first();

            // Pengecekan variable $username jika tidak ada di table mahasiswa
            if (!$username) {
                return redirect()->back()->with(['pesan' => 'Username tidak terdaftar']);
            }

            // jika username ada, langsung check password yang dikirim di form dan di table, hasilnya sama atau tidak
            $password = Hash::check($request->password, $username->password);

            // Pengecekan variable $password jika password tidak sama dengan yang dikirimkan
            if (!$password) {
                return redirect()->back()->with(['pesan' => 'Password tidak sesuai']);
            }

            // Jalankan fungsi auth jika berjasil melewati validasi di atas
            if (Auth::guard('mahasiswa')->attempt(['username' => $request->username, 'password' => $request->password])) {
                // Jika login berhasil
                return redirect()->back();
            } else {
                // Jika login gagal
                return redirect()->back()->with(['pesan' => 'Akun tidak terdaftar!']);
            }
        }
    }

    public function formRegister()
    {
        // Arahkan ke file user/register.blade.php
        return view('user.register');
    }

    public function register(Request $request)
    {
        // Masukkan semua data yg dikirim ke variable $data
        $data = $request->all();

        // Buat variable $validate kemudian isinya Validator::make(datanya, [nama_field => peraturannya])
        $validate = Validator::make($data, [
            'nim' => ['required', 'unique:mahasiswa'],
            'nama' => ['required', 'string'],
            'email' => ['required', 'email', 'string', 'unique:mahasiswa'],
            'username' => ['required', 'string', 'regex:/^\S*$/u', 'unique:mahasiswa'],
            'password' => ['required', 'min:6'],
            'telp' => ['required'],
        ]);

        // Pengecekan jika validate fails atau gagal
        if ($validate->fails()) {
            return redirect()->back()->withErrors($validate)->withInput();
        }

        // Mengecek email
        $email = Mahasiswa::where('email', $request->username)->first();

        // Pengecekan jika email sudah terdaftar
        if ($email) {
            return redirect()->back()->with(['pesan' => 'Email sudah terdaftar'])->withInput(['email' => 'asd']);
        }

        // Mengecek username
        $username = Mahasiswa::where('username', $request->username)->first();

        // Pengecekan jika username sudah terdaftar
        if ($username) {
            return redirect()->back()->with(['pesan' => 'Username sudah terdaftar'])->withInput(['username' => null]);
        }

        // Memasukkan data kedalam table mahasiswa
        Mahasiswa::create([
            'nim' => $data['nim'],
            'nama' => $data['nama'],
            'email' => $data['email'],
            'username' => $data['username'],
            'password' => Hash::make($data['password']),
            'telp' => $data['telp'],
        ]);

        // Kirim link verifikasi email
        // $link = URL::temporarySignedRoute('sipma.verify', now()->addMinutes(30), ['nim' => $data['nim']]);
        // Mail::to($data['email'])->send(new VerifikasiEmailUntukRegistrasiPengaduanmahasiswa($data['nama'], $link));

        // Arahkan ke route sipma.index
        return redirect()->route('sipma.index');
    }

    public function logout()
    {
        // Fungsi logout dengan guard('mahasiswa')
        Auth::guard('mahasiswa')->logout();

        // Arahkan ke route sipma.index
        return redirect()->route('sipma.index');
    }

    public function storePengaduan(Request $request)
    {
        // Pengecekan jika tidak ada mahasiswa yang sedang login
        if (!Auth::guard('mahasiswa')->user()) {
            return redirect()->back()->with(['pesan' => 'Login dibutuhkan!'])->withInput();
        }

        // Masukkan semua data yg dikirim ke variable $data
        $data = $request->all();

        // Buat variable $validate kemudian isinya Validator::make(datanya, [nama_field => peraturannya])
        $validate = Validator::make($data, [
            'judul_laporan' => ['required'],
            'isi_laporan' => ['required'],
            'tgl_kejadian' => ['required'],
            'lokasi_kejadian' => ['required'],
            'kategori_kejadian' => ['required'],
        ]);

        // Pengecekan jika validate fails atau gagal
        if ($validate->fails()) {
            return redirect()->back()->withErrors($validate)->withInput();
        }

        // Pengecekan jika ada file foto yang dikirim
        if ($request->file('foto')) {
            $data['foto'] = $request->file('foto')->store('assets/pengaduan', 'public');
        }

        // Set timezone waktu ke Asia/Bangkok
        date_default_timezone_set('Asia/Bangkok');

        // Membuat variable $pengaduan isinya Memasukkan data kedalam table Pengaduan
        $pengaduan = Pengaduan::create([
            'tgl_pengaduan' => date('Y-m-d h:i:s'),
            'nim' => Auth::guard('mahasiswa')->user()->nim,
            'judul_laporan' => $data['judul_laporan'],
            'isi_laporan' => $data['isi_laporan'],
            'tgl_kejadian' => $data['tgl_kejadian'],
            'lokasi_kejadian' => $data['lokasi_kejadian'],
            'kategori_kejadian' => $data['kategori_kejadian'],
            'foto' => $data['foto'] ?? '',
            'status' => '0',
        ]);

        // Pengecekan variable $pengaduan
        if ($pengaduan) {
            // Jika mengirim pengaduan berhasil
            return redirect()->route('sipma.laporan', 'me')->with(['pengaduan' => 'Berhasil terkirim!', 'type' => 'success']);
        } else {
            // Jika mengirim pengaduan gagal
            return redirect()->back()->with(['pengaduan' => 'Gagal terkirim!', 'type' => 'danger']);
        }
    }

    public function laporan($siapa = '')
    {
        // Membuat variable $terverifikasi isinya menghitung pengaduan status pending
        $terverifikasi = Pengaduan::where([['nim', Auth::guard('mahasiswa')->user()->nim], ['status', '!=', '0']])->get()->count();
        // Membuat variable $terverifikasi isinya menghitung pengaduan status proses
        $proses = Pengaduan::where([['nim', Auth::guard('mahasiswa')->user()->nim], ['status', 'proses']])->get()->count();
        // Membuat variable $terverifikasi isinya menghitung pengaduan status selesai
        $selesai = Pengaduan::where([['nim', Auth::guard('mahasiswa')->user()->nim], ['status', 'selesai']])->get()->count();

        // Masukkan 3 variable diatas ke dalam variable array $hitung
        $hitung = [$terverifikasi, $proses, $selesai];

        // Pengecekan jika ada parameter $siapa yang dikirimkan di url
        if ($siapa == 'me') {
            // Jika $siapa isinya 'me'
            $pengaduan = Pengaduan::where('nim', Auth::guard('mahasiswa')->user()->nim)->orderBy('tgl_pengaduan', 'desc')->get();

            // Arahkan ke file user/laporan.blade.php sebari kirim data pengaduan, hitung, siapa
            return view('user.laporan', ['pengaduan' => $pengaduan, 'hitung' => $hitung, 'siapa' => $siapa]);
        } else {
            // Jika $siapa kosong
            $pengaduan = Pengaduan::where([['nim', '!=', Auth::guard('mahasiswa')->user()->nim], ['status', '!=', '0']])->orderBy('tgl_pengaduan', 'desc')->get();

            // Arahkan ke file user/laporan.blade.php sebari kirim data pengaduan, hitung, siapa
            return view('user.laporan', ['pengaduan' => $pengaduan, 'hitung' => $hitung, 'siapa' => $siapa]);
        }
    }
}
