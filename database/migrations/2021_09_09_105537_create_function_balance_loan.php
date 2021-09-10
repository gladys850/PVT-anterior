<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFunctionBalanceLoan extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("CREATE or replace FUNCTION balance_loan(id_loan numeric)
        RETURNS numeric AS $$
        DECLARE balance_loan numeric;
        begin
             select l.amount_approved - total_payment_capital_loan(l.id) INTO balance_loan
             from loans l
             where l.id = id_loan;
        IF  balance_loan is NULL THEN
        return 0;
        else
        RETURN   balance_loan;
        END IF;
        END;
        $$ LANGUAGE plpgsql;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("DROP FUNCTION balance_loan");
    }
}
