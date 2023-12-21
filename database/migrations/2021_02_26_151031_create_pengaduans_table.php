<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePengaduansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pengaduan', function (Blueprint $table) {
            $table->id('id_pengaduan');
            $table->dateTime('tgl_pengaduan');
            $table->char('nim', 16);
            $table->string('judul_laporan');
            $table->text('isi_laporan');
            $table->dateTime('tgl_kejadian');
            $table->text('lokasi_kejadian');
            $table->enum('kategori_kejadian', ['sarpras', 'biaya', 'seksual', 'sosial']);
            $table->string('foto');
            $table->enum('status', ['0', 'proses', 'selesai']);

            $table->timestamps();

            $table->foreign('nim')->references('nim')->on('mahasiswa');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pengaduan');
    }
}
