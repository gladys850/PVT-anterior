<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLoanModalityParametersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loan_modality_parameters', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('procedure_modality_id'); // id modalidad de prestamo
            $table->foreign('procedure_modality_id')->references('id')->on('procedure_modalities');
            $table->float('debt_index',5,2)->nullable(); // indice de endeudamiento
            $table->smallInteger('quantity_ballots');// cantidad de boletas
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
        Schema::dropIfExists('loan_modality_parameters');
    }
}
