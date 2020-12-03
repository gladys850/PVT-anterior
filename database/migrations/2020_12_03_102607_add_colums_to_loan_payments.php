<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumsToLoanPayments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('loan_payments', function (Blueprint $table) {
            $table->text('voucher')->nullable(); // Comprobante del pago 
            $table->enum('paid_by', ['T', 'G']);// Pago realizado por Titular o Garante
            $table->unsignedBigInteger('affiliate_id')->unsigned(); // Id del afiliado
            $table->foreign('affiliate_id')->references('id')->on('affiliates');
            $table->unsignedBigInteger('payment_type_id')->unsigned(); // Id del tipo de pago 
            $table->foreign('payment_type_id')->references('id')->on('payment_types');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('loan_payments', function (Blueprint $table) {
            //
        });
    }
}
