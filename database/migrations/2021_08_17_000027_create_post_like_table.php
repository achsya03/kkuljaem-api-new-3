<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePostLikeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('post_like', function (Blueprint $table) {
            $table->id();
            $table->BigInteger('id_user')->unsigned()->nullable();
            $table->BigInteger('id_post')->unsigned()->nullable();
            // $table->BigInteger('id_quiz')->unsigned()->nullable();
            $table->string('uuid');
            $table->index(['uuid']);
            $table->foreign('id_user')->references('id')->on('users')->onDelete('cascade');
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
        Schema::dropIfExists('post_like');
    }
}
