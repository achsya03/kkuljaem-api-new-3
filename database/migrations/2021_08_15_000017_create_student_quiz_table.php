<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentQuizTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('student_quiz', function (Blueprint $table) {
            $table->id();
            $table->BigInteger('id_student')->unsigned()->nullable();
            $table->BigInteger('id_quiz')->unsigned()->nullable();
            $table->date('register_date')->nullable(); 
            $table->integer('nilai')->nullable();
            $table->string('uuid')->nullable();            
            $table->index(['uuid']);
            $table->foreign('id_student')->references('id')->on('students')->onDelete('cascade');
            $table->foreign('id_quiz')->references('id')->on('quiz')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('student_quiz');
    }
}
