<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLoansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loans', function (Blueprint $table) {
            $table->bigIncrements('id');// id unico
            $table->string('code')->nullable(); // para el correlativo
            $table->integer('disbursable_id');// id affiliado, id espouse
            $table->enum('disbursable_type',['affiliates', 'spouses']); // a quien se hara del desembolso//afiliado, conyugue
            $table->unsignedBigInteger('procedure_modality_id'); // id modalidad
            $table->foreign('procedure_modality_id')->references('id')->on('procedure_modalities');
            $table->integer('amount_disbursement')->nullable(); // monto a desembolsar
            $table->date('disbursement_date')->nullable(); //fecha de desembolso
            $table->integer('parent_loan_id')->nullable();  // id padre , loan padre
            $table->enum('parent_reason', ['refinanciado', 'reprogramado'])->nullable();// para indicar si es reprogramado y refinanciado 
            $table->date('request_date'); //fecha de solicitud
            $table->smallInteger('amount_request'); // monto solicitado
            $table->unsignedBigInteger('city_id');  // id lugar de la solicitud 
            $table->foreign('city_id')->references('id')->on('cities');
            $table->unsignedBigInteger('loan_interest_id')->nullable(false); // id del interes
            $table->foreign('loan_interest_id')->references('id')->on('loan_interests'); 
            $table->unsignedBigInteger('loan_state_id')->nullable(false); //id estado del tramite
            $table->foreign('loan_state_id')->references('id')->on('loan_states'); // estado de prestamo
            $table->smallInteger('amount_aproved')->nullable(); // monto aprobado
            $table->integer('loan_term'); // plazo del prestamo en meses
            $table->unsignedBigInteger('disbursement_type_id'); // id tipo de desembolso   
            $table->foreign('disbursement_type_id')->references('id')->on('payment_types'); 
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
        Schema::dropIfExists('loans');
    }
}
