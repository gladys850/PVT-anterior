<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLoanParametersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loan_parameters', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('offset_day'); //dia de desface
            $table->integer('livelihood_amount')->nullable(); ; // cantidad de sustento-->inf. estadistica
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
        Schema::dropIfExists('loan_parameters');
    }
}
