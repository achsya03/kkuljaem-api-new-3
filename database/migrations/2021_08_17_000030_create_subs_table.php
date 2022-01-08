<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subs', function (Blueprint $table) {
            $table->id();
            $table->BigInteger('id_user')->unsigned()->nullable();
            $table->BigInteger('id_packet')->unsigned()->nullable();
            $table->BigInteger('id_reference')->unsigned()->nullable();
            $table->integer('harga')->nullable();
            $table->integer('diskon')->nullable();
            $table->datetime('tgl_subs')->nullable();
            $table->datetime('tgl_akhir_bayar');
            #$table->string('jenis_jawaban')->nullable();
            $table->text('snap_token');
            $table->text('snap_url');
            $table->string('subs_status');
            $table->string('uuid');
            $table->index(['uuid']);
            $table->foreign('id_user')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('id_packet')->references('id')->on('packet')->onDelete('cascade');
            $table->foreign('id_reference')->references('id')->on('reference')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('subs');
    }
}
