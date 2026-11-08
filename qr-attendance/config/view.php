<?php

return [
    /*
    |--------------------------------------------------------------------------
    | View Storage Paths
    |--------------------------------------------------------------------------
    |
    | Most template engines store the compiled versions of the views on disk.
    | Here you may specify the paths that should be checked for your views.
    |
    */
    'paths' => [
        resource_path('views'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Compiled View Path
    |--------------------------------------------------------------------------
    |
    | This is where compiled Blade templates are stored. Use storage_path()
    | instead of realpath() so the path remains valid before the directory
    | exists (for example, immediately after extracting the project ZIP).
    |
    */
    'compiled' => env('VIEW_COMPILED_PATH', storage_path('framework/views')),
];
