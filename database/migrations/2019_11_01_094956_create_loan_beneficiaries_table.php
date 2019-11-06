<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLoanBeneficiariesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loan_beneficiaries', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('loan_id'); // id persona de referencia
            $table->foreign('loan_id')->references('id')->on('loans'); 
            $table->string('first_name');
            $table->string('last_name');
            $table->string('mothers_last')->nullable(); 
            $table->string('second_name')->nullable(); 
            $table->integer('identity_card');
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
        Schema::dropIfExists('loan_beneficiaries');
    }
}
