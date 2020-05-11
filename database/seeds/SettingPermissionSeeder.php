<?php

use Illuminate\Database\Seeder;
use App\Permission;

class SettingPermissionSeeder extends Seeder
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
                'name' => 'update-setting',
                'display_name' => 'Editar ajustes'
            ], [
                'name' => 'show-setting',
                'display_name' => 'Ver ajustes del sistema'
            ]
        ];
        foreach ($permissions as $permission) {
            Permission::firstOrCreate($permission);
        }
    }
}
