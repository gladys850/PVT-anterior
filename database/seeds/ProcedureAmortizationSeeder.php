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
                'amortizacion directa' => [
                    'type' => ['module_id' => $module->id,'name'=>'Amortización Directa','second_name'=>'Amort. DIRECTA'],
                    'modalities' => [ ['name'=>'AD Parcial','shortened'=>'ADP','requirements'=>[]],
                                      ['name'=>'AD Regular','shortened'=>'ADR','requirements'=>[]],
                                      ['name'=>'AD Adelantado','shortened'=>'ADA','requirements'=>[]],
                                      ['name'=>'AD Total','shortened'=>'ADT','requirements'=>[]],
                    ]
                ],
                'amortizacion complemento' => [
                    'type' => ['module_id' => $module->id,'name'=>'Amortización Complemento Económico','second_name'=>'Amort. CE'],
                    'modalities' => [ ['name'=>'ACE Parcial','shortened'=>'ACEP','requirements'=>[]],
                                      ['name'=>'ACE Total','shortened'=>'ACET','requirements'=>[]],
                    ]
                ],
                'amortizacion fondo' => [
                    'type' => ['module_id' => $module->id,'name'=>'Amortización Fondo de Retiro','second_name'=>'Amort. FR'],
                    'modalities' => [ ['name'=>'AFR Parcial','shortened'=>'AFRP','requirements'=>[]],
                                       ['name'=>'AFR Total','shortened'=>'AFRT','requirements'=>[]],
                    ]
                ],
                'amortizacion automatica' => [
                    'type' => ['module_id' => $module->id,'name'=>'Amortización Automática','second_name'=>'Amort. AUT'],
                    'modalities' => [ ['name'=>'AA Regular','shortened'=>'AAR','requirements'=>[]],
                                    ['name'=>'AA Parcial','shortened'=>'AAP','requirements'=>[]],
                                    ['name'=>'AA Adelantado','shortened'=>'AAA','requirements'=>[]],
                    ]
                ],
                'amortizacion por ajuste' => [
                    'type' => ['module_id' => $module->id,'name'=>'Amortización por Ajuste','second_name'=>'Amort. AJUST'],
                    'modalities' => [ ['name'=>'AAJ Adelantado','shortened'=>'AAJA','requirements'=>[]]
                    ]
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
