<?php

use Illuminate\Database\Seeder;
use App\Permission;

class UserPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissions = [
            [
                'name' => 'create-user',
                'display_name' => 'Crear usuarios'
            ], [
                'name' => 'update-user',
                'display_name' => 'Editar usuarios'
            ], [
                'name' => 'show-user',
                'display_name' => 'Ver usuarios'
            ], [
                'name' => 'delete-user',
                'display_name' => 'Eliminar usuarios'
            ]
        ];
        foreach ($permissions as $permission) {
            Permission::firstOrCreate($permission);
        }
    }
}
