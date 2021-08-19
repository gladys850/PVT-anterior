<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLoanPaymentsPlanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    { 
        Schema::create('loan_plan_payments', function (Blueprint $table) {
            $table->bigIncrements('id');// id unico
            $table->unsignedBigInteger('loan_id');
            $table->foreign('loan_id')->references('id')->on('loans');
            $table->unsignedBigInteger('user_id');
            $table->date('disbursement_date');
            $table->foreign('user_id')->references('id')->on('users');
            $table->unsignedSmallInteger('quota_number'); // numero de cuota
            $table->date('estimated_date'); // fecha estimada de pago
            $table->unsignedSmallInteger('days'); // numero de dias calculados
            $table->float('capital',10,2)->default(0); // pago a capital
            $table->float('interest',10,2)->default(0); // pago de interes
            $table->float('total_amount',10,2)->default(0); // pago total
            $table->float('balance',10,2)->default(0); // pago a capital
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('loan_plan_payments');
    }
}
