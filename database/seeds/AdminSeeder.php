<?php

use Illuminate\Database\Seeder;
use App\City;
use App\Role;
use App\User;
use App\Permission;

class AdminSeeder extends Seeder
{
    /**
    * Run the database seeds.
    *
    * @return void
    */
    public function run()
    {
        User::flushEventListeners();
        Role::flushEventListeners();
        $user = User::firstOrCreate([
            'username' => 'admin'
        ], [
            'first_name' => 'Usuario',
            'last_name' => 'Administrador',
            'password' => Hash::make('admin'),
            'position' => 'Administrador',
            'status' => 'active',
            'city_id' => City::first()->id
        ]);
        $role = Role::whereName('TE-admin')->first();
        $user->roles()->sync($role);
        $role->syncPermissions(['create-user', 'update-user', 'show-user', 'delete-user', 'show-record', 'delete-record', 'update-role', 'show-role']);
    }
}