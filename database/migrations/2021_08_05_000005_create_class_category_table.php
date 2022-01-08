<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClassCategoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('class_category', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->text('deskripsi');
            // $table->string('url_foto');
            // $table->string('foto_id');
            // $table->integer('jml_kelas');
            $table->string('uuid');
            $table->index(['uuid','nama']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('class_category');
    }
}
