<?php

use Illuminate\Database\Seeder;
use App\ProcedureModality;
use App\ProcedureType;
use App\LoanModalityParameter;
use App\Module;
class LoanModalityParameterSeeder extends Seeder
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
            ['procedure_modality_id' => $procedure_modality[1], 'debt_index' => 50,'quantity_ballots' => 3],
            ['procedure_modality_id' => $procedure_modality[2], 'debt_index' => 50,'quantity_ballots' => 3],
            ['procedure_modality_id' => $procedure_modality[3], 'debt_index' => 50,'quantity_ballots' => 3],
            // largo plazo
            ['procedure_modality_id' => $procedure_modality[4], 'debt_index' => 50, 'quantity_ballots' => 1],
            ['procedure_modality_id' => $procedure_modality[5], 'debt_index' => 50, 'quantity_ballots' => 1],
            ['procedure_modality_id' => $procedure_modality[6], 'debt_index' => 50, 'quantity_ballots' => 1],
            ['procedure_modality_id' => $procedure_modality[7], 'debt_index' => 50, 'quantity_ballots' => 1],
            // largo plazo CPOP
            ['procedure_modality_id' => $procedure_modality[8], 'debt_index' => 60, 'quantity_ballots' => 1],
            //anticipo
            ['procedure_modality_id' => $procedure_modality[9], 'debt_index' => 90,'quantity_ballots' => 1],
            ['procedure_modality_id' => $procedure_modality[10], 'debt_index' => 90,'quantity_ballots' => 1],
            //hipotecario
            ['procedure_modality_id' => $procedure_modality[11], 'debt_index' => 90, 'quantity_ballots' => 3],
            //hipotecario -CPOP
            ['procedure_modality_id' => $procedure_modality[12], 'debt_index' => 90, 'quantity_ballots' => 3]

        ];
        foreach ($data as $parameter_ballots) {
            LoanModalityParameter::firstOrCreate($parameter_ballots);
        }
    }
}
