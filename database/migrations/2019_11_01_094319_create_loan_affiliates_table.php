<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLoanAffiliatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loan_affiliates', function (Blueprint $table) {
            $table->unsignedBigInteger('loan_id')->unsigned();
            $table->foreign('loan_id')->references('id')->on('loans');
            $table->unsignedBigInteger('affiliate_id')->unsigned();
            $table->foreign('affiliate_id')->references('id')->on('affiliates');
            $table->unsignedMediumInteger('payment_percentage');// porcentaje de descuento
            $table->boolean('guarantor')->default(false);//si es garante
            $table->float('payable_liquid_calculated',10,2); //promedio liquido pagable calculado
            $table->unsignedMediumInteger('bonus_calculated'); //total bonos calculado
            $table->float('quota_refinance',5,2); //cuota de refinanciamiento
            $table->float('indebtedness_calculated',5,2); //indice de endeudamiento calculado
            $table->float('liquid_qualification_calculated',10,2); //liquido para calificación calculado
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('loan_affiliates');
    }
}
