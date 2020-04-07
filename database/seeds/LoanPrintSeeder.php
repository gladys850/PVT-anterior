<?php

use Illuminate\Database\Seeder;
use App\ProcedureType;
use App\LoanPrint;
use App\Role;

class LoanPrintSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $modality = [
            'Préstamo Anticipo' => [
                ['PRE-area-de-recepcion','PRE-regional-santa-cruz', 'PRE-regional-cochabamba', 'PRE-regional-oruro', 'PRE-regional-potosi', 'PRE-regional-sucre', 'PRE-regional-tarija', 'PRE-regional-trinidad', 'PRE-regional-cobija'],
            ]
        ];
        foreach ($modality as $procedure_type => $pro_type) {
            $procedure = ProcedureType::whereName($procedure_type)->first();
            foreach ($pro_type as $r => $role) {
                foreach($role as $rol){
                    $roles = Role::whereName($rol)->first();
                    LoanPrint::firstOrCreate([
                        'procedure_type_id' => $procedure->id,
                        'role_id' => $roles->id,
                        'print' => true
                    ]);
                }
            }
        }
    }
}
