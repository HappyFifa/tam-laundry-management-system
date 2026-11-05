<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLayananTable extends Migration
{
    public function up()
    {
        Schema::create('layanan', function (Blueprint $table) {
            $table->id();
            $table->string('nama_layanan', 50);
            $table->integer('harga_per_kg')->nullable();
            $table->integer('harga_satuan')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('layanan');
    }
}