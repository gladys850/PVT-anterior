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
            $table->integer('disbursable_id');// id affiliado, id espouse, id beneficiary
            $table->string('disbursable_type'); // a quien se hara del desembolso//afiliado, conyugue, beneficiario
            $table->unsignedBigInteger('procedure_modality_id'); // id modalidad
            $table->foreign('procedure_modality_id')->references('id')->on('procedure_modalities');
            $table->integer('amount_disbursement'); // monto a desembolsar
            $table->integer('parent_loand_id')->nullable();  // id padre , loan padre
            $table->enum('parent_reason', ['refinanciado', 'reprogramado'])->nullable();;// para indicar si es reprogramado y refinanciado 
            $table->date('request_date'); //fecha de solicitud
            $table->smallInteger('amount_request'); // monto solicitado
            $table->unsignedBigInteger('city_id');  // id lugar de la solicitud 
            $table->foreign('city_id')->references('id')->on('cities');
            $table->unsignedBigInteger('loan_interest_id'); // id del interes
            $table->foreign('loan_interest_id')->references('id')->on('loan_interests'); 
            $table->unsignedBigInteger('loan_state_id')->nullable(); //id estado del tramite
            $table->foreign('loan_state_id')->references('id')->on('loan_states'); // estado de prestamo
           // $table->integer('contract_id'); // datos del contrato
            $table->smallInteger('amount_aproved')->nullable(); // monto aprobado
            //$table->date('deadline'); // fecha tope
            $table->integer('loan_term'); // plazo del prestamo en meses
            $table->date('disbursement_date'); //fecha de desembolso
            $table->unsignedBigInteger('disbursement_type_id'); // id tipo de desembolso   
            $table->foreign('disbursement_type_id')->references('id')->on('payment_types'); 
            $table->date('modification_date')->nullable();; // fecha de modificacion del tramite 
          //  $table-> enum('mora', 'en proceso','aprobado','anulado','rechazado','desembolsado','liduidado','amortizando'); //estados del tramite suponiendo que es nuevo tramite 
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
