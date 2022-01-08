<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuestionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->text('pertanyaan_teks')->nullable();
            $table->text('url_gambar')->nullable();
            $table->string('gambar_id')->nullable();
            $table->text('url_file')->nullable();
            //$table->string('file_id')->nullable();
            $table->char('jawaban',1)->nullable();
            $table->string('jenis_jawaban')->nullable();
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
        Schema::dropIfExists('questions');
    }
}
