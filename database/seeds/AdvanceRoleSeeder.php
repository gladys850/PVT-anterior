<?php

use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use App\Module;
use App\Role;

class AdvanceRoleSeeder extends Seeder
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
        $receipt_permissions = ['update-affiliate-secondary', 'show-affiliate', 'show-loan', 'create-loan', 'create-address', 'update-address', 'delete-address'];
        $receipt_roles = ['Área de Recepción', 'Regional Santa Cruz', 'Regional Cochabamba', 'Regional Oruro', 'Regional Potosí', 'Regional Sucre', 'Regional Tarija', 'Regional Trinidad', 'Regional Cobija'];
        $sequence_permissions = ['update-affiliate-secondary', 'show-affiliate', 'show-loan', 'update-address', 'update-loan'];
        $sequence_roles = [
            [
                'name' => 'Calificación',
                'action' => 'Calificado',
                'sequence' => 2
            ], [
                'name' => 'Revisión Legal',
                'action' => 'Revisado',
                'sequence' => 3
            ], [
                'name' => 'Jefatura',
                'action' => 'Aprobado',
                'sequence' => 4
            ], [
                'name' => 'Aprobación Dirección',
                'action' => 'Aprobado',
                'sequence' => 5
            ], [
                'name' => 'Aprobación Legal',
                'action' => 'Aprobado',
                'sequence' => 6
            ], [
                'name' => 'Tesorería',
                'action' => 'Desembolsado',
                'sequence' => 7
            ], [
                'name' => 'Cobranzas',
                'action' => 'Liquidado',
                'sequence' => 8
            ]
        ];

        if ($module) {
            foreach ($receipt_roles as $role) {
                $role = Role::firstOrCreate([
                    'name' => $module->shortened . '-' . Str::slug($role, '-')
                ], [
                    'display_name' => $role,
                    'action' => 'Recepcionado',
                    'module_id' => $module->id,
                    'sequence_number' => $role == 'Área de Recepción' ? 1 : 0
                ]);
                $role->syncPermissions($receipt_permissions);
            }

            foreach ($sequence_roles as $role) {
                $role = Role::firstOrCreate([
                    'name' => $module->shortened . '-' . Str::slug($role['name'], '-')
                ], [
                    'display_name' => $role['name'],
                    'action' => $role['action'],
                    'module_id' => $module->id,
                    'sequence_number' => $role['sequence']
                ]);
                $role->syncPermissions(in_array($role['name'], ['Jefatura', 'Dirección']) ? array_merge($sequence_permissions, ['show-all-loan']) : $sequence_permissions);
            }
        }
    }
}
