<?php

use Illuminate\Database\Seeder;
use App\ProcedureType;
use App\RoleSequence;
use App\Role;

class RoleSequenceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        RoleSequence::flushEventListeners();
        $sequences = [
            'Préstamo Anticipo' => [
                ['PRE-regional-santa-cruz', 'PRE-regional-cochabamba', 'PRE-regional-oruro', 'PRE-regional-potosi', 'PRE-regional-sucre', 'PRE-regional-tarija', 'PRE-regional-trinidad', 'PRE-regional-cobija'],
                ['PRE-recepcion'],
                ['PRE-calificacion'],
                ['PRE-revision-legal'],
                ['PRE-jefatura'],
                ['PRE-aprobacion-direccion'],
                ['PRE-aprobacion-legal'],
                ['PRE-tesoreria'],
                ['PRE-cobranzas']
            ]
        ];
        foreach ($sequences as $procedure_type => $sequence) {
            $procedure = ProcedureType::whereName($procedure_type)->first();
            foreach ($sequence as $i => $current) {
                if ($i > 0) {
                    foreach ($current as $next_role) {
                        $previous = $sequence[$i-1];
                        $curr = Role::whereName($next_role)->first();
                        foreach ($previous as $role) {
                            $prev = Role::whereName($role)->first();
                            if ($curr && $prev) {
                                RoleSequence::firstOrCreate([
                                    'procedure_type_id' => $procedure->id,
                                    'role_id' => $prev->id,
                                    'next_role_id' => $curr->id
                                ]);
                            }
                        }
                    }
                }
            }
        }
    }
}
