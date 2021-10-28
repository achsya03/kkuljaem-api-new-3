<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBannerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('banners', function (Blueprint $table) {
            $table->id();
            $table->string('judul_banner');
            $table->text('url_web')->nullable();
            $table->string('web_id')->nullable();
            $table->text('url_mobile')->nullable();
            $table->string('mobile_id')->nullable();
            $table->text('deskripsi')->nullable();
            $table->string('label')->nullable();
            $table->string('link')->nullable();
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
        Schema::dropIfExists('banners');
    }
}
