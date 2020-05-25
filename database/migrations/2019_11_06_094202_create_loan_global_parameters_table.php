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
            $table->unsignedTinyInteger('offset_ballot_day'); //dia de desface de boletas
            $table->unsignedTinyInteger('offset_interest_day'); //dia de desface de interes por desembolso
            $table->unsignedSmallInteger('livelihood_amount')->nullable();// cantidad de sustento-->inf. estadistica
            $table->unsignedTinyInteger('min_service_years'); // minimo años de servicio
            $table->unsignedTinyInteger('min_service_years_adm'); // minimo años de servicio adm policial
            $table->unsignedTinyInteger('max_guarantor_active'); // maximo de garantias para el sector activo
            $table->unsignedTinyInteger('max_guarantor_passive'); // maximo de garantias para el sector pasivo
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
