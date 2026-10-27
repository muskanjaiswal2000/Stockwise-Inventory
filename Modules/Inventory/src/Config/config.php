<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Inventory Module Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may define all of the configuration settings for the Inventory module.
    |
    */

    'name' => 'Inventory',
    'version' => '1.0.0',
    'description' => 'Inventory module for the application',
    'author' => 'Your Name',
    'email' => 'your.email@example.com',
    'website' => 'https://example.com',

    /*
    |--------------------------------------------------------------------------
    | Module Settings
    |--------------------------------------------------------------------------
    |
    | Here you may define all of the settings for the Inventory module.
    |
    */

    'settings' => [
        'enabled' => true,
        'debug' => false,
        'cache' => true,
        'cache_ttl' => 3600,
    ],

    /*
    |--------------------------------------------------------------------------
    | Module Dependencies
    |--------------------------------------------------------------------------
    |
    | Here you may define all of the dependencies for the Inventory module.
    |
    */

    'dependencies' => [
        // 'Core',
        // 'Auth',
    ],

    /*
    |--------------------------------------------------------------------------
    | Module Permissions
    |--------------------------------------------------------------------------
    |
    | Here you may define all of the permissions for the Inventory module.
    |
    */

    // 'permissions' => [
    //     'view_inventory' => 'view inventory',
    //     'view_categories' => 'view categories',
    //     'manage_categories' => 'manage categories',
    //     'view_products' => 'view products',
    //     'create_products' => 'create products',
    //     'edit_products' => 'edit products',
    //     'delete_products' => 'delete products',
    //     'restore_products' => 'restore products',
    //     'view_suppliers' => 'view suppliers',
    //     'manage_suppliers' => 'manage suppliers',
    //     'view_stock' => 'view stock',
    //     'manage_stock' => 'manage stock',
    //     'manage_staff_permissions' => 'manage staff permissions',
    // ],

    'notifications' => [
        'admin_email' => env('ADMIN_NOTIFICATION_EMAIL', 'admin@gmail.com'),
        'queue' => env('INVENTORY_NOTIFICATION_QUEUE', 'notifications'),
        'ignored_domains' => array_values(array_filter(array_map(
            'trim',
            explode(',', env('INVENTORY_NOTIFICATION_IGNORED_DOMAINS', '.test,.local,.invalid')),
        ))),
    ],
]; 
