<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLoanContributionAdjustsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {  //tabla donde se almacena ajuste contribución del préstamo
        Schema::create('loan_contribution_adjusts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unsignedBigInteger('loan_id')->nullable();
            $table->foreign('loan_id')->references('id')->on('loans')->onDelete('cascade');
            $table->unsignedBigInteger('affiliate_id')->unsigned();
            $table->foreign('affiliate_id')->references('id')->on('affiliates');
            $table->morphs('adjustable'); 
            $table->enum('type_affiliate',['lender', 'guarantor','cosigner']); // tipificacion de asignacion de prest//titular, codeudor,garante
            $table->float('amount',10,2)->default(0);  // monto de ajuste al liquido para calificacion
            $table->enum('type_adjust',['adjust','liquid']);// tipificacion de monto// ajuste, liquido
            $table->date('period_date');// Fecha del periodo de la boleta
            $table->string('description')->nullable();// Descripcion por lo que se realiza el ajuste
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
        Schema::dropIfExists('loan_contribution_adjusts');
    }
}
