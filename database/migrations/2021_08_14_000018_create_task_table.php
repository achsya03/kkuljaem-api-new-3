<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTaskTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('task', function (Blueprint $table) {
            $table->id();
            $table->BigInteger('id_question')->unsigned()->nullable();
            $table->BigInteger('id_video')->unsigned()->nullable();
            $table->integer('number')->nullable();
            // $table->string('judul')->nullable();
            // $table->string('keterangan')->nullable();
            $table->string('uuid');
            $table->index(['uuid']);
            
            $table->foreign('id_question')->references('id')->on('questions')->onDelete('cascade');
            $table->foreign('id_video')->references('id')->on('video')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('task');
    }
}
