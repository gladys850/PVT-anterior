<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFunctionEgressFundRotatory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement(" CREATE or replace FUNCTION egress_fund_rotatory(id_fund_rotatory numeric)
        RETURNS numeric AS $$
        DECLARE amount_output numeric;
        begin
            SELECT sum(fro.amount_disbursed) INTO amount_output
            FROM fund_rotatories fr
            JOIN fund_rotatory_outputs fro ON fr.id = fro.fund_rotatory_id
            WHERE fro.deleted_at is null AND fr.id = id_fund_rotatory;
        
        IF amount_output is NULL THEN
        return 0;
        else
        RETURN  amount_output;
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
        Schema::dropIfExists('function_egress_fund_rotatory');
    }
}
