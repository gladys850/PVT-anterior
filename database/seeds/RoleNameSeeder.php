<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Role;
use App\Module;
class RoleNameSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //adicion de roles
        $module_id = Module::whereName('prestamos')->first()->id;
        $new_roles = [
            ['module_id' => $module_id, 'display_name' => 'Calificación', 'action' => 'Calificado'],
            ['module_id' => $module_id, 'display_name' => 'Revisión Legal', 'action' => 'Revisado e Impresión'],
            ['module_id' => $module_id, 'display_name' => 'Jefatura', 'action' => 'Revisado'],
            ['module_id' => $module_id, 'display_name' => 'Revisión Dirección', 'action' => 'Revisado'],
            ['module_id' => $module_id, 'display_name' => 'Aprobación Legal', 'action' => 'Aprobado y firma'],
            ['module_id' => $module_id, 'display_name' => 'Aprobación Dirección', 'action' => 'Aprobado'],
            ['module_id' => $module_id, 'display_name' => 'Tesorería', 'action' => 'Recepcionado'],
            ['module_id' => $module_id, 'display_name' => 'Cobranzas', 'action' => 'Recepcionado'],
        ];
        foreach ($new_roles  as $new_role) {
            Role::firstOrCreate($new_role);
        }
        //
        $roles = Role::get();
        foreach ($roles as $role)
        {
            $role->update(['name' => $role->module->shortened . '-' . Str::slug($role->display_name, '-')]);
        }
        $role = Role::where('display_name', 'Administrador')->first();
        $role->update(['name' => $role->module->shortened . '-' . 'admin']);
    }
}