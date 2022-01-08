<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOptionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('options', function (Blueprint $table) {
            $table->id();
            $table->BigInteger('id_question')->unsigned()->nullable();
            //$table->string('jawaban_id')->nullable();
            $table->text('jawaban_teks')->nullable();
            $table->text('url_gambar')->nullable();
            $table->string('gambar_id')->nullable();
            $table->text('url_file')->nullable();
            //$table->string('file_id')->nullable();
            $table->string('uuid');
            $table->index(['uuid']);
            $table->foreign('id_question')->references('id')->on('questions')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('options');
    }
}
