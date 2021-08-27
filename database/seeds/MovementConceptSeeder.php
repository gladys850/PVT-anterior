<?php

use Illuminate\Database\Seeder;
use App\MovementConcept;
use App\Role;
use App\User;

class MovementConceptSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::whereUsername('admin')->first();
        $role = Role::whereName('PRE-supervisor-tesoreria')->first();
        $movementConcepts = [
            [
                'name' => 'DESEMBOLSO ANTICIPO',
                'shortened'=>'DES-ANT-EG',
                'description' => 'Desembolso de préstamo modalidad Anticipo',
                'type'=> 'EGRESO',
                'abbreviated_supporting_document'=>'REC',
                'user_id'=>$user->id,
                'role_id'=>$role->id
            ],  [
                'name' => 'INGRESO FONDO ROTATORIO',
                'shortened'=>'FON-ROT-IN',
                'description' => 'Ingreso Fondo Rotatorio',
                'type'=> 'INGRESO',
                'abbreviated_supporting_document'=>'CH',
                'user_id'=>$user->id,
                'role_id'=>$role->id
            ], [
                'name' => 'CIERRE DE FONDO ROTATORIO',
                'shortened'=>'C-FON-ROT-EG',
                'description' => 'Cierre de gestión Fondo Rotatorio',
                'type'=> 'EGRESO',
                'abbreviated_supporting_document'=>'C',
                'user_id'=>$user->id,
                'role_id'=>$role->id
            ]
        ];
        foreach ($movementConcepts as $movementConcept) {
            MovementConcept::firstOrCreate($movementConcept);
        }
    }
}
