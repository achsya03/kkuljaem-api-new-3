<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVideoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('video', function (Blueprint $table) {
            $table->id();
            $table->BigInteger('id_content')->unsigned()->nullable();
            #$table->BigInteger('id_task')->unsigned()->nullable();
            $table->string('judul')->nullable();
            $table->text('keterangan')->nullable();
            //$table->integer('jml_pertanyaan')->nullable();
            $table->integer('jml_latihan')->nullable();
            $table->integer('jml_shadowing')->nullable();
            $table->string('url_video')->nullable();
            //$table->string('url_video_web')->nullable();
            $table->string('uuid');
            $table->index(['uuid']);
            $table->foreign('id_content')->references('id')->on('content')->onDelete('cascade');
            #$table->foreign('id_task')->references('id')->on('task')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('video');
    }
}
