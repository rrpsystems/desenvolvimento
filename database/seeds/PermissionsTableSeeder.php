<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\ModAdministradorRole;
use App\User;

class PermissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissions = [
            'connections-list',
            'connections-create',
            'connections-edit',
            'connections-delete',
            
            'pbx-list',
            'pbx-create',
            'pbx-edit',
            'pbx-delete',
            
            'extensions-list',
            'extensions-create',
            'extensions-edit',
            'extensions-delete',
            
            'users-list',
            'users-create',
            'users-edit',
            'users-delete',
            
            'roles-list',
            'roles-create',
            'roles-edit',
            'roles-delete'
         ];
 
         foreach ($permissions as $permission):
            Permission::create(['name' => $permission]);
        endforeach;

        Role::create(['name' => 'Root']);
        Role::create(['name' => 'Administrador']);
        Role::create(['name' => 'Usuario']);
        
        $role = Role::findByName('Administrador');
        $role->givePermissionTo($permissions);
        
        $root = User::find(1);
        $root->assignRole('Root');
        
        $admin = User::find(2);
        $admin->assignRole('Administrador');
        
        $user = User::find(3);
        $user->assignRole('Usuario');
    }
}
