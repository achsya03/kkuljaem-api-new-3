<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentAnswerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('student_answer', function (Blueprint $table) {
            $table->id();
            //$table->BigInteger('id_question')->unsigned()->nullable();
            $table->BigInteger('id_student_quiz')->unsigned()->nullable();
            $table->text('jawaban');
            $table->string('uuid');
            $table->index(['uuid']);
            //$table->foreign('id_question')->references('id')->on('questions')->onDelete('cascade');
            $table->foreign('id_student_quiz')->references('id')->on('student_quiz')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('student_answer');
    }
}
