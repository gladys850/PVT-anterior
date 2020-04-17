<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Pvt extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loan_interests', function (Blueprint $table) 
        {
            $table->bigIncrements('id');// id unico
            $table->unsignedBigInteger('procedure_modality_id'); // id modalidad
            $table->foreign('procedure_modality_id')->references('id')->on('procedure_modalities');
            $table->decimal('annual_interest',5,2); // interes anual  
            $table->decimal('penal_interest',5,2); // interes penal anual    
            $table->timestamps();
        });
        // adicionar para los estados del prestamo
        Schema::create('loan_states', function (Blueprint $table) 
        {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('description');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::dropIfExists('loan_interests');
        Schema::dropIfExists('loan_states');
    }
}
