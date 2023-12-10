<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Mail\VerifikasiEmailUntukRegistrasiPengaduanMahasiswa;
use App\Models\Mahasiswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

class EmailController extends Controller
{
    public function sendVerification()
    {
        $nik = Auth::guard('mahasiswa')->user()->nik;
        $email = Auth::guard('mahasiswa')->user()->email;
        $nama = Auth::guard('mahasiswa')->user()->nama;
        $link = URL::temporarySignedRoute('sipma.verify', now()->addMinutes(30), ['nik' => $nik]);
        Mail::to($email)->send(new VerifikasiEmailUntukRegistrasiPengaduanMahasiswa($nama, $link));

        return redirect()->back();
    }

    public function verify($nik, Request $request)
    {
        $mahasiswa = Mahasiswa::where('nik', $nik)->first();

        if ($mahasiswa->email_verified_at == null) {
            if ($request->hasValidSignature()) {

                date_default_timezone_set('Asia/Bangkok');

                $mahasiswa->update(['email_verified_at' => date('Y-m-d h:i:s')]);

                return view('User.Mail.success');
            } else {
                return view('User.Mail.failed');
            }
        } else {
            return view('User.Mail.failed');
        }
    }
}
