@extends('layouts.user')

@section('css')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<style>
    body {
        background: #800080;
    }

    .btn-purple {
        background: #800080;
        width: 100%;
        color: #fff;
        font-weight: 600;
    }

    .btn-purple:hover {
        background: #580058;
        width: 100%;
        color: #fff;
        font-weight: 600;
    }

    .btn-facebook {
        background: #3b66c4;
        width: 100%;
        color: #fff;
        font-weight: 600;
    }

    .btn-facebook:hover {
        background: #3b66c4;
        width: 100%;
        color: #fff;
        font-weight: 600;
    }

    .btn-google {
        background: #cf4332;
        width: 100%;
        color: #fff;
        font-weight: 600;
    }

    .btn-google:hover {
        background: #cf4332;
        width: 100%;
        color: #fff;
        font-weight: 600;
    }

</style>
@endsection

@section('title', 'Halaman Daftar')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-5">
            <h2 class="text-center text-white mb-0 mt-5">SIPMA</h2>
            <P class="text-center text-white mb-5">Sistem Informasi Pengaduan Mahasiswa</P>
            <div class="card mt-5">
                <div class="card-body">
                    <h2 class="text-center mb-4">FORM DAFTAR</h2>
                    <form action="{{ route('sipma.register') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <input type="number" value="{{ old('nim') }}" name="nim" placeholder="nim" class="form-control @error('nim') is-invalid @enderror">
                            @error('nim')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <input type="text" value="{{ old('nama') }}" name="nama" placeholder="Nama Lengkap" class="form-control @error('nama') is-invalid @enderror">
                            @error('nama')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <input type="email" value="{{ old('email') }}" name="email" placeholder="Email" class="form-control @error('email') is-invalid @enderror">
                            @error('email')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <input type="text" value="{{ old('username') }}" name="username" placeholder="Username" class="form-control @error('username') is-invalid @enderror">
                            @error('username')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <input type="password" name="password" placeholder="Password" class="form-control @error('password') is-invalid @enderror">
                            @error('password')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <input type="number" value="{{ old('telp') }}" name="telp" placeholder="No. Telp" class="form-control @error('telp') is-invalid @enderror">
                            @error('telp')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-purple">DAFTAR</button>
                    </form>
                </div>
            </div>
            @if (Session::has('pesan'))
                <div class="alert alert-danger my-2">
                    {{ Session::get('pesan') }}
                </div>
            @endif
            <a href="{{ route('sipma.index') }}" class="btn btn-light text-black mt-3" style="width: 100%; font-weight: 600">Kembali ke Halaman Utama</a>
        </div>
    </div>
</div>
@endsection
