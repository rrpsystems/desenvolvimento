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
            
            //dashboard
            'dsb_resumes-list',
            
            //reports
            'rep_bypbx-list',
            'rep_bygroups-list',
            'rep_bytenants-list',
            'rep_bysections-list',
            'rep_bydepartaments-list',
            'rep_byextensions-list',
            'rep_byaccountcodes-list',
            'rep_byphonebooks-list',
            'rep_bytrunks-list',

            //configurations   
            'cfg_pbx-list',
            'cfg_pbx-create',
            'cfg_pbx-edit',
            'cfg_pbx-delete',
            
            'cfg_prefixes-list',
            'cfg_prefixes-create',
            'cfg_prefixes-edit',
            'cfg_prefixes-delete',
            
            'cfg_routes-list',
            'cfg_routes-create',
            'cfg_routes-edit',
            'cfg_routes-delete',
            
            'cfg_rates-list',
            'cfg_rates-create',
            'cfg_rates-edit',
            'cfg_rates-delete',
            
            'cfg_trunks-list',
            'cfg_trunks-create',
            'cfg_trunks-edit',
            'cfg_trunks-delete',
            
            'cfg_groups-list',
            'cfg_groups-create',
            'cfg_groups-edit',
            'cfg_groups-delete',
            
            'cfg_tenants-list',
            'cfg_tenants-create',
            'cfg_tenants-edit',
            'cfg_tenants-delete',
            
            'cfg_sections-list',
            'cfg_sections-create',
            'cfg_sections-edit',
            'cfg_sections-delete',
            
            'cfg_departaments-list',
            'cfg_departaments-create',
            'cfg_departaments-edit',
            'cfg_departaments-delete',
            
            'cfg_extensions-list',
            'cfg_extensions-create',
            'cfg_extensions-edit',
            'cfg_extensions-delete',
            
            'cfg_accountcodes-list',
            'cfg_accountcodes-create',
            'cfg_accountcodes-edit',
            'cfg_accountcodes-delete',
            
            'cfg_phonebooks-list',
            'cfg_phonebooks-create',
            'cfg_phonebooks-edit',
            'cfg_phonebooks-delete',
            
            'cfg_users-list',
            'cfg_users-create',
            'cfg_users-edit',
            'cfg_users-delete',
            
            'cfg_roles-list',
            'cfg_roles-create',
            'cfg_roles-edit',
            'cfg_roles-delete',

            //maintenance
            'mnt_status-list',
            'mnt_rebilling-list',
            'mnt_calls-list',


         ];
 
         foreach ($permissions as $permission):
            Permission::create(['name' => $permission]);
        endforeach;

        //Role::create(['name' => 'Root']);
        Role::create(['name' => 'Master']);
        Role::create(['name' => 'Admin']);
        Role::create(['name' => 'Usuario']);
        
        $role = Role::findByName('Admin');
        $role->givePermissionTo($permissions);
        
        $root = User::find(1);
        $root->assignRole('Master');
        
        $admin = User::find(2);
        $admin->assignRole('Admin');
        
        $user = User::find(3);
        $user->assignRole('User');
    }
}
