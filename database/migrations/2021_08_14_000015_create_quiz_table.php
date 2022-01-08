<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuizTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quiz', function (Blueprint $table) {
            $table->id();
            $table->BigInteger('id_content')->unsigned()->nullable();
            #$table->BigInteger('id_exan')->unsigned()->nullable();
            $table->string('judul')->nullable();
            $table->text('keterangan')->nullable();
            $table->integer('jml_pertanyaan')->nullable();
            $table->string('uuid');
            $table->index(['uuid']);
            
            $table->foreign('id_content')->references('id')->on('content')->onDelete('cascade');
            #$table->foreign('id_exam')->references('id')->on('exam')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('quiz');
    }
}
