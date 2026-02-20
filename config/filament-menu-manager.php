<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Table Prefix
    |--------------------------------------------------------------------------
    | Optional prefix for all plugin database tables.
    */
    'table_prefix' => 'fmm_',

    /*
    |--------------------------------------------------------------------------
    | Default Menu Locations
    |--------------------------------------------------------------------------
    | Define default locations here. Each location has a handle (slug) and a
    | human-readable name. You can also define locations via the plugin API:
    |   FilamentMenuManagerPlugin::make()->locations(['primary' => 'Primary Menu'])
    */
    'locations' => [
        // 'primary' => 'Primary Menu',
        // 'footer'  => 'Footer Menu',
    ],

    /*
    |--------------------------------------------------------------------------
    | Models
    |--------------------------------------------------------------------------
    | You may swap out the default models for your own extended versions.
    */
    'models' => [
        'menu_location' => \NoteBrainsLab\FilamentMenuManager\Models\MenuLocation::class,
        'menu'          => \NoteBrainsLab\FilamentMenuManager\Models\Menu::class,
        'menu_item'     => \NoteBrainsLab\FilamentMenuManager\Models\MenuItem::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Eloquent Model Sources
    |--------------------------------------------------------------------------
    | Register Eloquent models that can be added as menu items.
    | Each model must use the HasMenuItems trait.
    */
    'model_sources' => [
        // \App\Models\Post::class,
        // \App\Models\Page::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Navigation
    |--------------------------------------------------------------------------
    */
    'navigation' => [
        'group' => 'Settings',
        'icon'  => 'heroicon-o-bars-3',
        'sort'  => 99,
        'label' => 'Menu Manager',
    ],

    /*
    |--------------------------------------------------------------------------
    | Max Nesting Depth
    |--------------------------------------------------------------------------
    | Maximum allowed nesting depth for menu items. Set to null for unlimited.
    */
    'max_depth' => 2,

    /*
    |--------------------------------------------------------------------------
    | Auto Save
    |--------------------------------------------------------------------------
    | Enable or disable the auto-save feature. When enabled, changes are
    | saved automatically after a short debounce delay (milliseconds).
    */
    'auto_save'          => true,
    'auto_save_debounce' => 800,

];
