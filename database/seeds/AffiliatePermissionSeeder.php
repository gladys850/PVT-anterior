<?php

use Illuminate\Database\Seeder;
use App\Permission;

class AffiliatePermissionSeeder extends Seeder
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
                'name' => 'create-address',
                'display_name' => 'Crear dirección'
            ], [
                'name' => 'update-address',
                'display_name' => 'Editar datos de dirección'
            ], [
                'name' => 'show-address',
                'display_name' => 'Ver direcciones'
            ], [
                'name' => 'delete-address',
                'display_name' => 'Eliminar dirección'
            ]
        ];
        foreach ($permissions as $permission) {
            Permission::firstOrCreate($permission);
        }
    }
}
