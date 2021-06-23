<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateImportPaymentsSenasirTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('import_payments_senasir', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('period_id')->unsigned();
            $table->foreign('period_id')->references('id')->on('periods');
            $table->string('registration');
            $table->string('registration_dh')->nullable();
            $table->float('amount',10,2);
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
        Schema::dropIfExists('import_payments_senasir');
    }
}
