<?php

use Illuminate\Database\Seeder;
use App\LoanInterval;
use App\Module;
use App\ProcedureType;

class LoanIntervalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $module = Module::whereName('prestamos')->first();
        $procedure_type = ProcedureType::whereModule_id($module->id)->get();$i=1; $pro_type=[];
        foreach($procedure_type as $pro){ 
            $pro_type[$i] = $pro->id; $i++; 
        }
        $loan_intervals = [
            ['maximum_amount' => 25000,'minimum_amount' => 2001,'maximum_term' => 24,'minimum_term' => 4,'procedure_type_id' => $pro_type[1]],
            ['maximum_amount' => 150000,'minimum_amount' => 25001,'maximum_term' => 96,'minimum_term' => 25,'procedure_type_id' => $pro_type[2]],
            ['maximum_amount' => 2000,'minimum_amount' => 200,'maximum_term' => 3,'minimum_term' => 1,'procedure_type_id' => $pro_type[3]],
            ['maximum_amount' => 700000,'minimum_amount' => 25001,'maximum_term' => 240,'minimum_term' => 25,'procedure_type_id' => $pro_type[4]],     
        ];
        foreach ($loan_intervals as $loan_interval) {
            LoanInterval::firstOrCreate($loan_interval);
        }
    }
}
