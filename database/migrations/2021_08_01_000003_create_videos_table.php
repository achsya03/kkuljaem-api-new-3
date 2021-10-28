<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVideosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('videos', function (Blueprint $table) {
            $table->id();
            #$table->string('judul_video')->nullable();
            $table->date('jadwal')->nullable();
            $table->string('url_video')->nullable();
            //$table->string('url_video_web')->nullable();
            #$table->string('video_id')->nullable();
            #$table->string('durasi_video')->nullable();
            $table->string('uuid');
            $table->index(['uuid','jadwal']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('videos');
    }
}
