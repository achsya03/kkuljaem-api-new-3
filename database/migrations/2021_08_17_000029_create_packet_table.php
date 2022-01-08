<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePacketTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('packet', function (Blueprint $table) {
            $table->id();
            $table->string('nama')->default('-');
            $table->string('deskripsi')->default('-');
            $table->integer('lama_paket')->nullable();
            $table->integer('harga')->nullable();
            $table->char('status_aktif',1)->nullable();
            #$table->string('jenis_jawaban')->nullable();
            $table->string('uuid');
            $table->index(['uuid']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('packet');
    }
}
