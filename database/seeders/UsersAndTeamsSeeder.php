<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Hash;
use Str;

class UsersAndTeamsSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $user = User::create(
            [
                'name' => 'Super Admin',
                'email' => 'super_admin_user@test.com',
                'email_verified_at' => now(),
                'password' =>  Hash::make('password', ['rounds' => 12]),
                'remember_token' => Str::random(10),
            ]
        );
        
        if (!$user) {
            return;
        }

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // create permissions
        Permission::create(['name' => 'manage admins']);
        Permission::create(['name' => 'manage investors']);

        // create roles and assign created permissions

        // this can be done as separate statements
        $role = Role::create(['name' => 'admin']);
        $role->givePermissionTo('manage investors');

        // or may be done by chaining
        $role = Role::create(['name' => 'investor']);

        $role = Role::create(['name' => 'super-admin']);
        $role->givePermissionTo(Permission::all());

        $user->assignRole('super-admin');
    }
}