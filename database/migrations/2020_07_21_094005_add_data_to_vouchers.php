<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDataToVouchers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vouchers', function (Blueprint $table) {
            $table->unsignedBigInteger('voucher_number')->nullable(); // numero de voucher
            $table->text('description')->nullable(); // descripcion por parte de tesoreria
            $table->dropColumn('bank_pay_number', 'bank');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('vouchers', function (Blueprint $table) {
            $table->dropColumn('voucher_number', 'description');
            $table->text('bank')->nullable();
            $table->text('bank_pay_number')->nullable();
        });
    }
}
