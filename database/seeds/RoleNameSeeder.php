<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Role;

class RoleNameSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles = Role::get();
        foreach ($roles as $role)
        {
            $role->update(['name' => $role->module->shortened . '-' . Str::slug($role->display_name, '-')]);
        }
        $role = Role::where('display_name', 'Administrador')->first();
        $role->update([
            'name' => $role->module->shortened . '-' . 'admin',
            'sequence_number' => 0
        ]);
    }
}