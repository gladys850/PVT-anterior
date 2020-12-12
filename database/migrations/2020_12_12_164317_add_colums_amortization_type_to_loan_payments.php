<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumsAmortizationTypeToLoanPayments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('loan_payments', function (Blueprint $table) {
            $table->unsignedBigInteger('amortization_type_id')->unsigned(); // Id del tipo de cobro
            $table->foreign('amortization_type_id')->references('id')->on('amortization_types');
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
