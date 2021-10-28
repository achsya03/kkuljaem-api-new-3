<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClassesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('classes', function (Blueprint $table) {
            $table->id();
            $table->BigInteger('id_class_category')->unsigned()->nullable();
            // $table->BigInteger('id_quiz')->unsigned()->nullable();
            $table->string('nama')->nullable();
            $table->text('deskripsi')->nullable();
            $table->text('url_web')->nullable();
            $table->string('web_id')->nullable();
            $table->text('url_mobile')->nullable();
            $table->string('mobile_id')->nullable();
            $table->integer('jml_video')->nullable();
            $table->integer('jml_kuis')->nullable();
            $table->char('status_tersedia',1)->nullable();
            //$table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
        $table->timestamp('updated_at')->nullable()/*->default(DB::raw('CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP'))*/;
            $table->string('uuid');
            $table->index(['uuid','nama']);
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->foreign('id_class_category')->references('id')->on('class_category')->onDelete('cascade');
            // $table->foreign('id_quiz')->references('id')->on('content_quiz')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('classes');
    }
}
