<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePesertaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('peserta', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid');
            $table->string('nama');
            $table->string('tempat_lahir')->nullable();
            $table->date('tanggal_lahir');
            $table->integer('umur');
            $table->text('alamat');
            $table->integer('durasi_asuransi')->nullable(); // dalam bulan
            $table->date('tanggal_mulai_asuransi')->nullable();
            $table->date('tanggal_selesai_asuransi')->nullable();
            $table->enum('status_peserta', ['pending', 'diterima', 'tolak'])->default('pending');
            $table->date('approved_peserta_at')->nullable();
            $table->string('approved_peserta_by')->nullable();
            $table->enum('status_dokumen', ['pending', 'diterima', 'tolak'])->default('pending');
            $table->date('approved_dokumen_at')->nullable();
            $table->string('approved_dokumen_by')->nullable();
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->string('deleted_by')->nullable();
            $table->date('deleted_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('peserta');
    }
}
