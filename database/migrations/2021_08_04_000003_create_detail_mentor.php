<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDetailMentor extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detail_mentors', function (Blueprint $table) {
            $table->id();
            $table->BigInteger('id_users')->unsigned()->nullable();
            $table->text('bio')->nullable();
            $table->date('awal_mengajar')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->string('uuid');
            $table->index(['uuid']);
            //$table->foreign('id_users')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('detail_mentors');
    }
}
