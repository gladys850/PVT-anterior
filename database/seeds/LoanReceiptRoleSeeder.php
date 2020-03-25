<?php

use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use App\Module;
use App\Role;

class LoanReceiptRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Role::flushEventListeners();
        $module = Module::whereName('prestamos')->first();
        $permissions = ['update-affiliate-secondary', 'show-affiliate', 'show-loan', 'create-loan', 'create-address', 'update-address', 'delete-address'];
        $roles = ['Recepción', 'Regional Santa Cruz', 'Regional Cochabamba', 'Regional Oruro', 'Regional Potosí', 'Regional Sucre', 'Regional Tarija', 'Regional Trinidad', 'Regional Cobija'];

        if ($module) {
            foreach ($roles as $role) {
                $role = Role::firstOrCreate([
                    'name' => $module->shortened . '-' . Str::slug($role, '-'),
                    'display_name' => $role,
                    'action' => 'Recepcionado',
                    'module_id' => $module->id
                ]);
                $role->syncPermissions($permissions);
            }
        }
    }
}
