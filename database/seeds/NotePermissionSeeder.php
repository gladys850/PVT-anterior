<?php

use Illuminate\Database\Seeder;

class NotePermissionSeeder extends Seeder
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
                'name' => 'update-note',
                'display_name' => 'Actualizar notas'
            ], [
                'name' => 'delete-note',
                'display_name' => 'Eliminar notas'
            ]
        ];
        foreach ($permissions as $permission) {
            Permission::firstOrCreate($permission);
        }
    }
}
