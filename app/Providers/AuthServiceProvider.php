<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::before(function ($user, $ability) {
            return $user->hasRole('Root') ? true : null;
        });
        //

        Gate::define('dashboard', function ($user) {
            $permissions = $user->getAllPermissions();
            $perm = '';
            
            foreach($permissions as $permission):
                if( substr($permission->name,0,4) == 'dsb_'):
                    $perm = $permission->name;
                    return true;
                endif;
            endforeach;

            return $user->can($perm);
        });
        
        Gate::define('reports', function ($user) {
            $permissions = $user->getAllPermissions();
            $perm = '';
            
            foreach($permissions as $permission):
                if( substr($permission->name,0,4) == 'rep_'):
                    $perm = $permission->name;
                    return true;
                endif;
            endforeach;

            return $user->can($perm);
        });
        
        Gate::define('settings', function ($user) {
            $permissions = $user->getAllPermissions();
            $perm = '';
            
            foreach($permissions as $permission):
                if( substr($permission->name,0,4) == 'cfg_'):
                    $perm = $permission->name;
                    return true;
                endif;
            endforeach;

            return $user->can($perm);
        });
        
        Gate::define('maintenances', function ($user) {
            $permissions = $user->getAllPermissions();
            $perm = '';
            
            foreach($permissions as $permission):
                if( substr($permission->name,0,4) == 'mnt_'):
                    $perm = $permission->name;
                    return true;
                endif;
            endforeach;

            return $user->can($perm);
        });

    }
}
