<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLoanGuarantorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    /*public function up()
    {
        Schema::create('loan_guarantors', function (Blueprint $table) {
            $table->integer('loan_id')->unsigned();
            $table->foreign('loan_id')->references('id')->on('loans');
            $table->integer('affiliate_id')->unsigned(); //  es para el registro del garante que debe ser un afiliado
            $table->foreign('affiliate_id')->references('id')->on('affiliates');
        });
    }*/

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    /*public function down()
    {
        Schema::dropIfExists('loan_guarantors');
    }*/
}
