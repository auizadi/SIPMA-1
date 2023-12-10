@extends('layouts.admin')

@section('title', 'Halaman mahasiswa')

@section('css')
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.23/css/jquery.dataTables.min.css">
@endsection

@section('header', 'Data mahasiswa')

@section('content')
    <table id="mahasiswaTable" class="table">
        <thead>
            <tr>
                <th>No</th>
                <th>nim</th>
                <th>Nama</th>
                <th>Username</th>
                <th>Telp</th>
                <th>Detail</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($mahasiswa as $k => $v)
            <tr>
                <td>{{ $k += 1 }}</td>
                <td>{{ $v->nim }}</td>
                <td>{{ $v->nama }}</td>
                <td>{{ $v->username }}</td>
                <td>{{ $v->telp }}</td>
                <td><a href="{{ route('mahasiswa.show', $v->nim) }}" style="text-decoration: underline">Lihat</a></td>
            </tr>
            @endforeach
        </tbody>
    </table>
@endsection

@section('js')
    <script src="https://cdn.datatables.net/1.10.23/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#mahasiswaTable').DataTable();
        } );
    </script>
@endsection
