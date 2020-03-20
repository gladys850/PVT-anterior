<?php

use Illuminate\Database\Seeder;
use App\Role;
use App\Module;

class RoleSequenceNumberSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $module_id = Module::whereName('prestamos')->first()->id;
        $roles = Role::whereModule_id($module_id)->get();
        foreach($roles as $role)
        {
            switch($role->display_name){
                case 'Cobranzas':
                    $role->update(['sequence_number' => 9]);
                    break;
                case 'Tesorería':
                    $role->update(['sequence_number' => 8]);
                    break;
                case 'Aprobación Dirección':
                    $role->update(['sequence_number' => 7]);
                    break;
                case 'Aprobación Legal':
                    $role->update(['sequence_number' => 6]);
                    break;
                case 'Revisión Dirección':
                    $role->update(['sequence_number' => 5]);
                    break;
                case 'Jefatura':
                    $role->update(['sequence_number' => 4]);
                    break;
                case 'Revisión Legal':
                    $role->update(['sequence_number' => 3]);
                    break;
                case 'Calificación':
                    $role->update(['sequence_number' => 2]);
                    break;
                case 'Recepción':
                    $role->update(['sequence_number' => 1]);
                    break;
                case 'Regional Santa Cruz':
                    $role->update(['sequence_number' => 0]);
                    break;
                case 'Regional Cochabamba':
                    $role->update(['sequence_number' => 0]);
                    break;
                case 'Regional Oruro':
                    $role->update(['sequence_number' => 0]);
                    break;
                case 'Regional Potosí':
                    $role->update(['sequence_number' => 0]);
                    break;
                case 'Regional Sucre':
                    $role->update(['sequence_number' => 0]);
                    break;
                case 'Regional Tarija':
                    $role->update(['sequence_number' => 0]);
                    break;
                case 'Regional Trinidad':
                    $role->update(['sequence_number' => 0]);
                    break;
                case 'Regional Cobija':
                    $role->update(['sequence_number' => 0]);
                    break;
            }
        }
    }
}
