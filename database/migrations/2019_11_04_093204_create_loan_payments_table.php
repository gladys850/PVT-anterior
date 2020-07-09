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
            $table->id();
            $table->unsignedBigInteger('loan_id'); 
            $table->foreign('loan_id')->references('id')->on('loans')->onDelete('cascade');
            $table->date('estimated_date'); // fecha estimada de pago
            $table->unsignedSmallInteger('quota_number'); // numero de cuota, cuando sea necesario se repite
            // de las siguientes 5 columnas se obtendra el total pagado
            $table->float('estimated_quota',10,2); // cuota estimada
            $table->float('penal_payment',10,2)->default(0); //pago penal
            $table->float('accumulated_payment',10,2)->default(0); //pago interés acumulado
            $table->float('interest_payment',10,2)->default(0); // pago de interes corriente
            $table->float('capital_payment',10,2)->default(0); // pago de capital
            $table->float('penal_remaining',10,2)->default(0); // interés penal previo
            $table->float('accumulated_remaining',10,2)->default(0); // interés acumulado previo
            $table->unsignedBigInteger('voucher_number')->nullable(); // numero de comprobante
            $table->unsignedBigInteger('receipt_number')->nullable(); // numero de recibo
            $table->text('description')->nullable(); // descripcion del pago 
            $table->timestamps();
            $table->softDeletes();
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
