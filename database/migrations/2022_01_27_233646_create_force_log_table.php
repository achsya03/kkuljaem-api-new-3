<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateForceLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('force_log', function (Blueprint $table) {
            $table->id();            
            $table->BigInteger('id_detail_student')->unsigned()->nullable();
            $table->BigInteger('id_detail_mentor')->unsigned()->nullable();
            //$table->string('jawaban_id')->nullable();
            $table->text('note')->nullable();
            $table->date('tgl_awal')->nullable();
            $table->date('tgl_edit')->nullable();
            //$table->string('file_id')->nullable();
            $table->string('uuid');
            $table->index(['uuid']);
            $table->foreign('id_detail_student')->references('id')->on('detail_student')->onDelete('cascade');
            $table->foreign('id_detail_mentor')->references('id')->on('detail_mentor')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('force_log');
    }
}
