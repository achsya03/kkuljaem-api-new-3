<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment', function (Blueprint $table) {
            $table->id();
            $table->BigInteger('id_subs')->unsigned()->nullable();
            //$table->string('number');
            $table->datetime('tgl_pembayaran');
            $table->string('transaction_id');
            $table->string('method');
            $table->string('status');
            $table->float('amount');
            $table->string('token');
            $table->json('payloads');
            $table->string('payment_type');
            // $table->string('va_number');
            // $table->string('vendor_name');
            // $table->string('biller_code');
            // $table->string('bill_key');
            $table->string('uuid');
            $table->index(['uuid']);
            $table->foreign('id_subs')->references('id')->on('subs')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payment');
    }
}
