<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommentAlertTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comment_alert', function (Blueprint $table) {
            $table->id();
            $table->BigInteger('id_user')->unsigned()->nullable();
            $table->BigInteger('id_comment')->unsigned()->nullable();
            // $table->BigInteger('id_quiz')->unsigned()->nullable();
            $table->text('komentar')->nullable();
            $table->char('alert_status',1)->nullable();
            $table->string('uuid');
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->index(['uuid']);
            $table->foreign('id_user')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('id_comment')->references('id')->on('comment')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('comment_alert');
    }
}
