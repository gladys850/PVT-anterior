<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSismuTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sismu', function (Blueprint $table) {
            $table->bigIncrements('id');// id unico
            $table->string('code')->nullable();//codigo
            $table->float('amount_approved',8,2)->nullable(); // monto aprobado
            $table->unsignedSmallInteger('loan_term')->nullable(); // plazo del prestamo en meses
            $table->float('balance',8,2)->nullable(); // saldo
            $table->float('estimated_quota',8,2)->nullable(); // cuota
            $table->unsignedBigInteger('loan_id');  //id del prestamo 
            $table->foreign('loan_id')->references('id')->on('loans');
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
        Schema::dropIfExists('sismu');
    }
}
