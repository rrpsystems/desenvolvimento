<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Title
    |--------------------------------------------------------------------------
    |
    | Here you can change the default title of your admin panel.
    |
    | For more detailed instructions you can look here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/#61-title
    |
    */

    'title' => 'BeneTelecom',
    'title_prefix' => '',
    'title_postfix' => '',

    /*
    |--------------------------------------------------------------------------
    | Logo
    |--------------------------------------------------------------------------
    |
    | Here you can change the logo of your admin panel.
    |
    | For more detailed instructions you can look here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/#62-logo
    |
    */

    'logo' => '<b>Bene</b>Telecom',
    'logo_img' => 'vendor/adminlte/dist/img/logo.png',
    'logo_img_class' => 'brand-image-xl',
    'logo_img_xl' => null,
    'logo_img_xl_class' => 'brand-image-xs',
    'logo_img_alt' => 'AdminLTE',

    /*
    |--------------------------------------------------------------------------
    | Layout
    |--------------------------------------------------------------------------
    |
    | Here we change the layout of your admin panel.
    |
    | For more detailed instructions you can look here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/#63-layout
    |
    */

    'layout_topnav' => null,
    'layout_boxed' => null,
    'layout_fixed_sidebar' => true,
    'layout_fixed_navbar' => true,
    'layout_fixed_footer' => null,

    /*
    |--------------------------------------------------------------------------
    | Extra Classes
    |--------------------------------------------------------------------------
    |
    | Here you can change the look and behavior of the admin panel.
    |
    | For more detailed instructions you can look here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/#64-classes
    |
    */

    'classes_body' => 'text-sm',
    'classes_brand' => '',
    'classes_brand_text' => '',
    'classes_content_header' => 'container-fluid',
    'classes_content' => 'container-fluid',
    'classes_sidebar' => 'sidebar-dark-primary elevation-4',
    'classes_sidebar_nav' => '',
    'classes_topnav' => 'navbar-white navbar-light',
    'classes_topnav_nav' => 'navbar-expand-md',
    'classes_topnav_container' => 'container',

    /*
    |--------------------------------------------------------------------------
    | Sidebar
    |--------------------------------------------------------------------------
    |
    | Here we can modify the sidebar of the admin panel.
    |
    | For more detailed instructions you can look here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/#65-sidebar
    |
    */

    'sidebar_mini' => true,
    'sidebar_collapse' => false,
    'sidebar_collapse_auto_size' => false,
    'sidebar_collapse_remember' => false,
    'sidebar_collapse_remember_no_transition' => true,
    'sidebar_scrollbar_theme' => 'os-theme-light',
    'sidebar_scrollbar_auto_hide' => 'l',
    'sidebar_nav_accordion' => true,
    'sidebar_nav_animation_speed' => 300,

    /*
    |--------------------------------------------------------------------------
    | Control Sidebar (Right Sidebar)
    |--------------------------------------------------------------------------
    |
    | Here we can modify the right sidebar aka control sidebar of the admin panel.
    |
    | For more detailed instructions you can look here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/#66-control-sidebar-right-sidebar
    |
    */

    'right_sidebar' => false,
    'right_sidebar_icon' => 'fas fa-cogs',
    'right_sidebar_theme' => 'dark',
    'right_sidebar_slide' => true,
    'right_sidebar_push' => true,
    'right_sidebar_scrollbar_theme' => 'os-theme-light',
    'right_sidebar_scrollbar_auto_hide' => 'l',

    /*
    |--------------------------------------------------------------------------
    | URLs
    |--------------------------------------------------------------------------
    |
    | Here we can modify the url settings of the admin panel.
    |
    | For more detailed instructions you can look here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/#67-urls
    |
    */

    'use_route_url' => false,

    'dashboard_url' => 'home',

    'logout_url' => 'logout',

    'login_url' => 'login',

    'register_url' => 'register',

    'password_reset_url' => 'password/reset',

    'password_email_url' => 'password/email',

    /*
    |--------------------------------------------------------------------------
    | Laravel Mix
    |--------------------------------------------------------------------------
    |
    | Here we can enable the Laravel Mix option for the admin panel.
    |
    | For more detailed instructions you can look here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/#68-laravel-mix
    |
    */

    'enabled_laravel_mix' => false,

    /*
    |--------------------------------------------------------------------------
    | Menu Items
    |--------------------------------------------------------------------------
    |
    | Here we can modify the sidebar/top navigation of the admin panel.
    |
    | For more detailed instructions you can look here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/#69-menu
    |
    */
       
    'menu' => [

            [
                'text'  => 'Resume',
                'route'  => 'resumes.index',
                'active'    => ['/dashboards/resumes*'],
                'icon'  => 'fas fa-fw fa-archive',
                'can'  => 'calls-list',
            ],
    
            [
                'text'      => 'Reports',
                'submenu'   => [
                    [
                        'text'  => 'By PBX',
                        'route'  => 'bypbx.index',
                        'active'    => ['/reports/bypbx*'],
                        'icon'  => 'fas fa-fw fa-archive',
                        'can'  => 'byextensions-list',
                    ],
                    [
                        'text'  => 'By Extensions',
                        'route'  => 'byextensions.index',
                        'active'    => ['/reports/byextensions*'],
                        'icon'  => 'fas fa-fw fa-archive',
                        'can'  => 'byextensions-list',
                    ],
                    [
                        'text'  => 'By Trunks',
                        'route'  => 'bytrunks.index',
                        'active'    => ['/reports/bytrunks*'],
                        'icon'  => 'fas fa-fw fa-archive',
                        'can'  => 'bytrunks-list',
                    ],
                ],
            ],    
        
        [
            'text' => 'settings',
            'icon'  => 'fas fa-cogs',
            'submenu'   => [
                [
                    'text'  => 'Pbx',
                    'route'  => 'pbx.index',
                    'active'    => ['/configs/pbx*'],
                    'icon'  => 'fas fa-fw fa-building',
                    'can'  => 'pbx-list',
                ],
                [
                    'text'  => 'Prefixes',
                    'route'  => 'prefixes.index',
                    'active'    => ['/configs/prefixes*'],
                    'icon'  => 'fas fa-fw fa-map-marker-alt',
                    'can'  => 'pbx-list',
                ],
                [
                    'text'  => 'Routes',
                    'route'  => 'routes.index',
                    'active'    => ['/configs/routes*'],
                    'icon'  => 'fas fa-fw fa-route',
                    'can'  => 'pbx-list',
                ],
                [
                    'text'  => 'Rates',
                    'route'  => 'rates.index',
                    'active'    => ['/configs/rates*'],
                    'icon'  => 'fas fa-fw fa-dollar-sign',
                    'can'  => 'pbx-list',
                ],
                [
                    'text'  => 'Trunks',
                    'route'  => 'trunks.index',
                    'active'    => ['/configs/trunks*'],
                    'icon'  => 'fas fa-fw fa-road',
                    'can'  => 'trunks-list',
                ],
                [
                    'text'  => 'Groups',
                    'route'  => 'groups.index',
                    'active'    => ['/configs/groups*'],
                    'icon'  => 'fas fa-fw fa-users',
                    'can'  => 'groups-list',
                ],
                [
                    'text'  => 'Tenants',
                    'route'  => 'tenants.index',
                    'active'    => ['/configs/tenants*'],
                    'icon'  => 'fas fa-fw fa-city',
                    'can'  => 'tenants-list',
                ],
                [
                    'text'  => 'Sections',
                    'route'  => 'sections.index',
                    'active'    => ['/configs/sections*'],
                    'icon'  => 'fas fa-fw fa-sitemap',
                    'can'  => 'sections-list',
                ],
                [
                    'text'  => 'Departaments',
                    'route'  => 'departaments.index',
                    'active'    => ['/configs/departaments*'],
                    'icon'  => 'fas fa-fw fa-project-diagram',
                    'can'  => 'departaments-list',
                ],
                [
                    'text'  => 'Extensions',
                    'route'  => 'extensions.index',
                    'active'    => ['/configs/extensions*'],
                    'icon'  => 'fas fa-fw fa-phone',
                    'can'  => 'extensions-list',
                ],
                [
                    'text'  => 'Accountcodes',
                    'route'  => 'accountcodes.index',
                    'active'    => ['/configs/accountcodes*'],
                    'icon'  => 'fas fa-fw fa-key',
                    'can'  => 'accountcode-list',
                ],
                [
                    'text'  => 'Phonebooks',
                    'route'  => 'phonebooks.index',
                    'active'    => ['/configs/phonebooks*'],
                    'icon'  => 'fas fa-fw fa-address-book',
                    'can'  => 'accountcode-list',
                ],
                [
                    'text'  => 'Usuarios',
                    'route'  => 'users.index',
                    'active'    => ['/configs/users*'],
                    'icon'  => 'fas fa-fw fa-user',
                    'can'  => 'users-list',
                ],
                [
                    'text'  => 'Permissions',
                    'route'  => 'roles.index',
                    'active'    => ['/configs/roles*'],
                    'icon'  => 'fas fa-fw fa-lock',
                    'can'  => 'roles-list',
                ],
            ],
        ],

        [
            'text' => 'Maintenance',
            'icon'  => 'fas fa-tools',
            'submenu'   => [
                [
                    'text'  => 'Status',
                    'route'  => 'status.index',
                    'active'    => ['/maintenance/status*'],
                    'icon'  => 'fas fa-exclamation-triangle',
                    'can'  => 'calls-list',
                ],
                [
                    'text'  => 'Rebilling',
                    'route'  => 'rebilling.index',
                    'active'    => ['/maintenance/rebilling*'],
                    'icon'  => 'fas fa-funnel-dollar',
                    'can'  => 'calls-list',
                ],
                [
                    'text'  => 'Database Calls',
                    'route'  => 'calls.index',
                    'active'    => ['/maintenance/calls*'],
                    'icon'  => 'fas fa-fw fa-database',
                    'can'  => 'calls-list',
                ],
            ],
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Menu Filters
    |--------------------------------------------------------------------------
    |
    | Here we can modify the menu filters of the admin panel.
    |
    | For more detailed instructions you can look here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/#610-menu-filters
    |
    */

    'filters' => [
        JeroenNoten\LaravelAdminLte\Menu\Filters\HrefFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\SearchFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\ActiveFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\SubmenuFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\ClassesFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\GateFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\LangFilter::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Plugins Initialization
    |--------------------------------------------------------------------------
    |
    | Here we can modify the plugins used inside the admin panel.
    |
    | For more detailed instructions you can look here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/#611-plugins
    |
    */

    'plugins' => [
        [
            'name' => 'Datatables',
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdn.datatables.net/v/bs/dt-1.10.18/datatables.min.js',
                ],
                [
                    'type' => 'css',
                    'asset' => false,
                    'location' => '//cdn.datatables.net/v/bs/dt-1.10.18/datatables.min.css',
                ],
            ],
        ],
        [
            'name' => 'Select2',
            'active' => true,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => true,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js',
                ],
                [
                    'type' => 'css',
                    'asset' => true,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.css',
                ],
            ],
        ],
        [
            'name' => 'Chartjs',
            'active' => true,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => true,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.0/Chart.bundle.min.js',
                ],
            ],
        ],
        [
            'name' => 'Sweetalert2',
            'active' => true,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => true,
                    'location' => '//cdn.jsdelivr.net/npm/sweetalert2@8',
                ],
            ],
        ],
        [
            'name' => 'Pace',
            'active' => false,
            'files' => [
                [
                    'type' => 'css',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/pace/1.0.2/themes/blue/pace-theme-center-radar.min.css',
                ],
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/pace/1.0.2/pace.min.js',
                ],
            ],
        ],
    ],
];
