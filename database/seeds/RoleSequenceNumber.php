<?php

use Illuminate\Database\Seeder;
use App\Role;
use App\Module;

class RoleSequenceNumber extends Seeder
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
                case 'Área de Recepción':
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
