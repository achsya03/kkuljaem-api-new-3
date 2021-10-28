<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('words', function (Blueprint $table) {
            $table->id();
            $table->date('jadwal')->default("2001/01/01")->nullable();
            $table->string('hangeul')->nullable();
            $table->string('pelafalan')->nullable();
            $table->text('penjelasan')->nullable();
            $table->text('url_pengucapan')->nullable();
            $table->string('pengucapan_id')->nullable();
            $table->string('uuid');
            $table->index(['uuid']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('words');
    }
}
