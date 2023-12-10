<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Mahasiswa;
use App\Models\Pengaduan;
use Illuminate\Http\Request;

class MahasiswaController extends Controller
{
    public function index()
    {
        $mahasiswa = Mahasiswa::all();

        return view('Admin.mahasiswa.index', ['mahasiswa' => $mahasiswa]);
    }

    public function show($nim)
    {
        $mahasiswa = Mahasiswa::where('nim', $nim)->first();

        return view('Admin.mahasiswa.show', ['mahasiswa' => $mahasiswa]);
    }

    public function destroy(Mahasiswa $mahasiswa)
    {
        $pengaduan = Pengaduan::where('nim', $mahasiswa->nim)->first();

        if (!$pengaduan) {
            $mahasiswa->delete();

            return redirect()->route('mahasiswa.index');
        } else {
            return redirect()->back()->with(['notif' => 'Can\'t delete. mahasiswa has a relationship!']);
        }
    }
}
