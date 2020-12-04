<?php

use Illuminate\Database\Seeder;
use App\Module;
use App\ProcedureModality;
use App\ProcedureType;
class ProcedureAmortizationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $module = Module::whereName('prestamos')->first();
        $data = [
            'procedures' => [
                'amortizacion manual' => [
                    'type' => ['module_id' => $module->id,'name'=>'Amortización Manual','second_name'=>'Amort.  Manual'],
                    'modalities' => [ ['name'=>'Amortización Manual','shortened'=>'AR','requirements'=>[],
                    ]]
                ],
                'amortizacion complemento' => [
                    'type' => ['module_id' => $module->id,'name'=>'Amortización Complemento Economico','second_name'=>'Amort. CE'],
                    'modalities' => [ ['name'=>'Amortización Complemento Economico','shortened'=>'ACE','requirements'=>[],
                    ]]
                ],
                'amortizacion fondo' => [
                    'type' => ['module_id' => $module->id,'name'=>'Amortización Fondo de Retiro','second_name'=>'Amort. FR'],
                    'modalities' => [ ['name'=>'Amortización Fondo de Retiro','shortened'=>'AFR','requirements'=>[],
                    ]]
                ],
                'amortizacion automatica' => [
                    'type' => ['module_id' => $module->id,'name'=>'Amortización Automática','second_name'=>'Amort.  Aut'],
                    'modalities' => [ ['name'=>'Amortización Automática','shortened'=>'AP','requirements'=>[],
                    ]]
                ],
            ]
        ];
        foreach ($data['procedures'] as $procedure) {
            $new_procedure = ProcedureType::firstOrCreate($procedure['type']);
            foreach ($procedure['modalities'] as $modality) {
                $new_modality = ProcedureModality::firstOrCreate([
                    'procedure_type_id' => $new_procedure->id,
                    'name'=>$modality['name'],
                    'shortened'=>$modality['shortened']
                ]);
            }
        }
    }
}
