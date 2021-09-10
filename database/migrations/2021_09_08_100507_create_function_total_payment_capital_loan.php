<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFunctionTotalPaymentCapitalLoan extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("CREATE or replace FUNCTION total_payment_capital_loan(id_loan numeric)
        RETURNS numeric AS $$
        DECLARE sum_capital_payment numeric;
        begin
             select sum(lp.capital_payment) INTO sum_capital_payment
             from loans l 
             join loan_payments lp on l.id =lp.loan_id
             where lp.deleted_at is null and (l.id = id_loan and lp.state_id = 3) or (l.id = id_loan and lp.state_id = 4);
        IF sum_capital_payment is NULL THEN
        return 0;
        else
        RETURN  sum_capital_payment;
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
        DB::statement("DROP FUNCTION total_payment_capital_loan");
    }
}
