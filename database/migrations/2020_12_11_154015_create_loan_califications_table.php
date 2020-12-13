<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLoanCalificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loan_califications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('loan_id')->unsigned();
            $table->foreign('loan_id')->references('id')->on('loans');           
            $table->float('ballots',10,2); //boleta
            $table->float('security_bonus',10,2);//Bono seguridad
            $table->float('east_bond',10,2); //Bono oreiente
            $table->float('border_bond',10,2);//Bono frontera
            $table->float('bonus_charge',10,2); //Bono Cargo
            $table->float('bonus_rent_dignity',10,2);// Bono renta dignidad
            $table->float('other_bonus',10,2);// otros bono como boono reintegro
            $table->date('ballot_period');//periodo al que pertenece 
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
        Schema::dropIfExists('loan_califications');
    }
}
