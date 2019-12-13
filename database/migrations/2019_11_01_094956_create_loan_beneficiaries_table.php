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
            $table->unsignedBigInteger('city_identity_card_id')->nullable();  // id lugar de la solicitud 
            $table->foreign('city_identity_card_id')->references('id')->on('cities');
            $table->string('identity_card');
            $table->string('last_name');
            $table->string('mothers_last_name')->nullable(); 
            $table->string('first_name');
            $table->string('second_name')->nullable(); 
            $table->string('surname_husband')->nullable(); 
            $table->date('birth_date')->nullable();
            $table->enum('gender', ['M', 'F'])->nullable();; // genero 
            $table->enum('civil_status', ['C', 'S', 'V', 'D'])->nullable(); //estado civil
            $table->string('phone_number')->nullable(); 
            $table->string('cell_phone_number')->nullable(); 
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
