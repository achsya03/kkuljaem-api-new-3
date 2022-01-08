<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShadowingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shadowing', function (Blueprint $table) {
            $table->id();
            $table->BigInteger('id_word')->unsigned()->nullable();
            $table->BigInteger('id_video')->unsigned()->nullable();
            $table->integer('number')->nullable();
            $table->string('uuid');
            $table->index(['uuid']);
            
            $table->foreign('id_word')->references('id')->on('words')->onDelete('cascade');
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
        Schema::dropIfExists('shadowing');
    }
}
