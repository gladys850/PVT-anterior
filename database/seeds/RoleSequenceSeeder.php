<?php

use Illuminate\Database\Seeder;
use App\ProcedureType;
use App\Role;
use App\Module;
use App\RoleSequence;

class RoleSequenceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // de regionales a recepción
        $module_id = Module::whereName('prestamos')->first()->id;
        $type_id = ProcedureType::whereName('Préstamo Anticipo')->first()->id;
        $reception_id = Role::whereDisplay_name('Recepción')->first()->id;
        $regional = Role::where('module_id',$module_id)->where('display_name', 'like', '%'.'Regional'.'%')->get();
        $role_sequence = RoleSequence::get();
        foreach ($regional as $reg)
        {
            if(count($role_sequence)>0){
                RoleSequence::firstOrCreate(['procedure_type_id' => $type_id,'role_id' =>$reg->id,'next_role_id' =>$reception_id]);
                RoleSequence::firstOrCreate(['procedure_type_id' => $type_id,'role_id' =>$reception_id,'next_role_id' =>$reg->id]);

            }else{
                RoleSequence::insert(['procedure_type_id' => $type_id,'role_id' =>$reg->id,'next_role_id' =>$reception_id]);
                RoleSequence::insert(['procedure_type_id' => $type_id,'role_id' =>$reception_id,'next_role_id' =>$reg->id]);
            }
        }
        // de Recepción
        $legal_review_id = Role::where('module_id',$module_id)->where('display_name','Revisión Legal')->first()->id; // Revisión legal
        $approved_id = Role::where('module_id',$module_id)->where('display_name','Aprobación Dirección')->first()->id;// aprobación dirección
        $treasury_id = Role::where('module_id',$module_id)->where('display_name','Tesorería')->first()->id;// Tesorería
        $collect_id = Role::where('module_id',$module_id)->where('display_name','Cobranzas')->first()->id;// cobranzas

        $workflow = [
            // hacia adelante
            ['procedure_type_id' => $type_id, 'role_id' => $reception_id, 'next_role_id' => $legal_review_id],// de recepción a revision legal
            ['procedure_type_id' => $type_id, 'role_id' => $legal_review_id, 'next_role_id' => $approved_id],// de revisión legal a aproación dirección
            ['procedure_type_id' => $type_id, 'role_id' => $approved_id, 'next_role_id' => $treasury_id], //de aprobación dirección a tesorería
            ['procedure_type_id' => $type_id, 'role_id' => $treasury_id, 'next_role_id' => $collect_id], //de tesorería a cobranzas
            // hacia atras
            ['procedure_type_id' => $type_id, 'role_id' => $collect_id, 'next_role_id' => $treasury_id], //de cobranzas vuelve a tesorería
            ['procedure_type_id' => $type_id, 'role_id' => $treasury_id, 'next_role_id' => $approved_id], // de tesorería vuelve a aprobación dirección
            ['procedure_type_id' => $type_id, 'role_id' => $legal_review_id, 'next_role_id' => $reception_id],// de revisión legal vuelve a recepción
        ];
        foreach ($workflow  as $flow) {
            if(count($role_sequence)>0){
                RoleSequence::firstOrCreate($flow);
            }else{
                RoleSequence::insert($flow);
            }
        }
    }
}
