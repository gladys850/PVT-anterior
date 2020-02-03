<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLoanAffiliatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loan_affiliates', function (Blueprint $table) {
            $table->integer('loan_id')->unsigned();
            $table->foreign('loan_id')->references('id')->on('loans');
            $table->integer('affiliate_id')->unsigned();
            $table->foreign('affiliate_id')->references('id')->on('affiliates');
            $table->unsignedSmallInteger('payment_percentage')->default(100);
			$table->boolean('guarantor')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('loan_affiliates');
    }
}
