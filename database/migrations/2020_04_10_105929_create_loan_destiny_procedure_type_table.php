<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLoanDestinyProcedureTypeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loan_destiny_procedure_type', function (Blueprint $table) {
            $table->unsignedBigInteger('procedure_type_id');
            $table->foreign('procedure_type_id')->references('id')->on('procedure_types');
            $table->unsignedBigInteger('loan_destiny_id');
            $table->foreign('loan_destiny_id')->references('id')->on('loan_destinies');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('loan_destiny_procedure_type');
    }
}
