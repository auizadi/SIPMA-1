<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\LaporanController;
use App\Http\Controllers\Admin\MahasiswaController;
use App\Http\Controllers\Admin\PengaduanController;
use App\Http\Controllers\Admin\PetugasController;
use App\Http\Controllers\Admin\TanggapanController;
use App\Http\Controllers\User\EmailController;
use App\Http\Controllers\User\SocialController;
use App\Http\Controllers\User\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [UserController::class, 'index'])->name('sipma.index');

Route::post('/mahasiswa/sendverification', [EmailController::class, 'sendVerification'])->name('sipma.sendVerification');
Route::get('/mahasiswa/verify/{nik}', [EmailController::class, 'verify'])->name('sipma.verify');

Route::middleware(['isMahasiswa'])->group(function () {
    // Pengaduan
    Route::post('/store', [UserController::class, 'storePengaduan'])->name('sipma.store');
    Route::get('/laporan/{siapa?}', [UserController::class, 'laporan'])->name('sipma.laporan');

    // Logout Mahasiswa
    Route::get('/logout', [UserController::class, 'logout'])->name('sipma.logout');
});

Route::middleware(['guest'])->group(function () {
    // Login Mahasiswa
    Route::post('/login/auth', [UserController::class, 'login'])->name('sipma.login');

    // Register
    Route::get('/register', [UserController::class, 'formRegister'])->name('sipma.formRegister');
    Route::post('/register/auth', [UserController::class, 'register'])->name('sipma.register');

    // Media Sosial
    Route::get('auth/{provider}', [SocialController::class, 'redirectToProvider'])->name('sipma.auth');
    Route::get('auth/{provider}/callback', [SocialController::class, 'handleProviderCallback']);
});

Route::prefix('admin')->group(function () {

    Route::middleware(['isAdmin'])->group(function () {
        // Petugas
        Route::resource('petugas', PetugasController::class);

        // Mahasiswa
        Route::resource('mahasiswa', MahasiswaController::class);

        // Laporan
        Route::get('laporan', [LaporanController::class, 'index'])->name('laporan.index');
        Route::post('getLaporan', [LaporanController::class, 'getLaporan'])->name('laporan.getLaporan');
        Route::get('laporan/cetak/{from}/{to}', [LaporanController::class, 'cetakLaporan'])->name('laporan.cetakLaporan');
    });

    Route::middleware(['isPetugas'])->group(function () {
        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

        // Pengaduan
        Route::resource('pengaduan', PengaduanController::class);

        // Taanggapan
        Route::post('tanggapan/createOrUpdate', [TanggapanController::class, 'createOrUpdate'])->name('tanggapan.createOrUpdate');

        // Logout
        Route::get('/logout', [AdminController::class, 'logout'])->name('admin.logout');
    });

    Route::middleware(['isGuest'])->group(function () {
        Route::get('/', [AdminController::class, 'formLogin'])->name('admin.formLogin');
        Route::post('/login', [AdminController::class, 'login'])->name('admin.login');
    });
});
