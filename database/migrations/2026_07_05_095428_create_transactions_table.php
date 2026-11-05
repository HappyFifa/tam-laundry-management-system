<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('layanan_id')->constrained('layanan')->cascadeOnDelete();
            
            $table->string('no_resi', 20)->unique();
            $table->string('metode_kirim');
            $table->string('metode_pembayaran')->default('Online');
            $table->text('alamat')->nullable();
            $table->string('latitude', 50)->nullable();
            $table->string('longitude', 50)->nullable();
            $table->float('total_berat')->nullable();
            $table->string('nama_kurir')->nullable();
            $table->text('catatan')->nullable(); // Kolom catatan
            $table->integer('ewp_menit')->default(0);
            $table->string('status_pengerjaan');
            $table->timestamp('waktu_mulai_cuci')->nullable();
            $table->timestamp('waktu_selesai_cuci')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('transactions');
    }
}