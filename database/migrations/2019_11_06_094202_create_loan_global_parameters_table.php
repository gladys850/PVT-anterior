<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLoanGlobalParametersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loan_global_parameters', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('offset_ballot_day'); //dia de desface de boletas
            $table->integer('offset_interest_day'); //dia de desface de interes por desembolso
            $table->integer('livelihood_amount')->nullable();// cantidad de sustento-->inf. estadistica
            $table->integer('min_service_years'); // minimo años de servicio
            $table->integer('min_service_years_adm'); // minimo años de servicio adm policial
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('loan_global_parameters');
    }
}
