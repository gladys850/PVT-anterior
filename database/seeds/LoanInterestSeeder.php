<?php

use Illuminate\Database\Seeder;
use App\ProcedureModality;
use App\ProcedureType;
use App\LoanInterest;
use App\Module;

class LoanInterestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $procedure_modality=[]; $i=1;
        $module = Module::whereName('prestamos')->first();
        $procedure_type = ProcedureType::whereModule_id($module->id)->get();
        foreach($procedure_type as $pro_type){ 
            $pro_mo = ProcedureModality::whereProcedure_type_id($pro_type->id)->get(); 
            foreach($pro_mo as $datos) { 
                $procedure_modality[$i]=$datos->id; $i++; 
            } 
        }
        $data = [
            //corto plazo
            ['procedure_modality_id' => $procedure_modality[1], 'annual_interest' => 36,'penal_interest' => 6],
            ['procedure_modality_id' => $procedure_modality[2], 'annual_interest' => 36,'penal_interest' => 6],
            ['procedure_modality_id' => $procedure_modality[3], 'annual_interest' => 20,'penal_interest' => 6],
            ['procedure_modality_id' => $procedure_modality[4], 'annual_interest' => 20,'penal_interest' => 6],
            ['procedure_modality_id' => $procedure_modality[5], 'annual_interest' => 20,'penal_interest' => 6],
           // largo plazo
            ['procedure_modality_id' => $procedure_modality[6], 'annual_interest' => 13.20, 'penal_interest' => 6],
            ['procedure_modality_id' => $procedure_modality[7], 'annual_interest' => 13.20, 'penal_interest' => 6],
            ['procedure_modality_id' => $procedure_modality[8], 'annual_interest' => 13.20, 'penal_interest' => 6],
            ['procedure_modality_id' => $procedure_modality[9], 'annual_interest' => 13.20, 'penal_interest' => 6],
            ['procedure_modality_id' => $procedure_modality[10], 'annual_interest' => 13.20, 'penal_interest' => 6],
            //hipotecario
            ['procedure_modality_id' => $procedure_modality[11], 'annual_interest' => 9, 'penal_interest' => 6]
        ];
            foreach ($data as $loan_interest) {
                LoanInterest::create($loan_interest);
            } 
    }
}
