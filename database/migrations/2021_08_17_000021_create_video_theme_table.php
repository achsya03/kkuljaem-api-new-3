<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVideoThemeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('video_theme', function (Blueprint $table) {
            $table->id();
            $table->BigInteger('id_video')->unsigned()->nullable();
            $table->BigInteger('id_theme')->unsigned()->nullable();
            // $table->BigInteger('id_quiz')->unsigned()->nullable();
            $table->string('uuid');
            $table->index(['uuid']);
            $table->foreign('id_video')->references('id')->on('video')->onDelete('cascade');
            $table->foreign('id_theme')->references('id')->on('theme')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('video_theme');
    }
}
