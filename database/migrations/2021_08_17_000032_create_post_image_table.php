<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePostImageTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('post_image', function (Blueprint $table) {
            $table->id();
            $table->BigInteger('id_post')->unsigned()->nullable();
            $table->text('url_gambar')->nullable();
            $table->string('gambar_id')->nullable();
            #$table->string('jenis_jawaban')->nullable();
            $table->string('uuid');
            $table->index(['uuid']);
            $table->foreign('id_post')->references('id')->on('post')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('post_image');
    }
}
