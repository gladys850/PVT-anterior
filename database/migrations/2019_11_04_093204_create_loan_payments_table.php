<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLoanPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    { // table amortization
        Schema::create('loan_payments', function (Blueprint $table) {
            $table->unsignedBigInteger('loan_id'); 
            $table->foreign('loan_id')->references('id')->on('loans'); 
            $table->unsignedBigInteger('affiliate_id'); // registro del depositante  
            $table->foreign('affiliate_id')->references('id')->on('affiliates');
            $table->date('pay_date')->nullable(); // fecha de pago
            $table->date('estimated_date')->nullable(); // fecha estimada de pago
            $table->integer('quota_number')->nullable(); // numero de cuota, cuando sea necesario se repite
            // de las siguientes 5 columnas se obtendra el total pagado
            $table->float('estimated_fee',10,2)->nullable(); // cuota estimada
            $table->float('capital_payment',10,2)->nullable(); // pago de capital
            $table->float('interest_payment',10,2)->nullable(); // pago de interes
            $table->float('penal_payment',10,2)->nullable(); //pago penal
            $table->float('accumulation_interest',10,2)->nullable(); // otros cobros, interes acumulado
            $table->integer('voucher_number')->nullable(); // numero de comprobante
            $table->enum('payment_type', ['EFECTIVO', 'CHEQUE', 'DESCUENTO AUTOMATICO','DEPOSITO EN CUENTA']); // tipos de pago 
            $table->integer('receipt_number')->nullable(); // numero de recibo
            $table->string('description')->nullable(); // descripcion del pago 
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
        Schema::dropIfExists('loan_payments');
    }
}
