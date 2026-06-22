<?php

return [

    /*
    |----------------------------------------------------------------------
    | Google Drive Credentials
    |----------------------------------------------------------------------
    */
    'credentials_path'     => env('GOOGLE_DRIVE_CREDENTIALS_PATH'),
    'client_id'            => env('GOOGLE_DRIVE_CLIENT_ID'),
    'client_secret'        => env('GOOGLE_DRIVE_CLIENT_SECRET'),
    'refresh_token'        => env('GOOGLE_DRIVE_REFRESH_TOKEN'),
    'shared_drive_id'      => env('GOOGLE_DRIVE_SHARED_DRIVE_ID'),
    'root_folder_id'       => env('GOOGLE_DRIVE_ROOT_FOLDER_ID'),
    'application_name'     => env('GOOGLE_DRIVE_APP_NAME', 'Laravel Drive Files'),
    'public_links_enabled' => env('GOOGLE_DRIVE_PUBLIC_LINKS_ENABLED', false),

    /*
    |----------------------------------------------------------------------
    | Database
    |----------------------------------------------------------------------
    */
    'table_name'           => 'drive_files',
    'auto_load_migrations' => true,

    /*
    |----------------------------------------------------------------------
    | Host App Models (override to your own)
    |----------------------------------------------------------------------
    */
    'models' => [
        'user'   => 'App\\Models\\User',
        'office' => 'App\\Models\\Office',
    ],

    /*
    |----------------------------------------------------------------------
    | Upload Limits
    |----------------------------------------------------------------------
    */
    'max_file_size_mb'   => (int) env('GOOGLE_DRIVE_MAX_FILE_SIZE_MB', 100),
    'allowed_mime_types' => [
        'image/jpeg', 'image/png', 'image/gif', 'image/webp',
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'text/plain',
    ],

    /*
    |----------------------------------------------------------------------
    | Routes
    |----------------------------------------------------------------------
    */
    'routes' => [
        'enabled' => env('DRIVE_ROUTES_ENABLED', true),
        'prefix'  => env('DRIVE_ROUTES_PREFIX', 'api/v1/drive'),
        'name'    => env('DRIVE_ROUTES_NAME', 'api.v1.drive.'),
    ],

    /*
    |----------------------------------------------------------------------
    | Authentication Middleware
    |----------------------------------------------------------------------
    */
    'auth' => [
        'middleware'       => ['auth:sanctum'],
        'extra_middleware' => [],
    ],

    /*
    |----------------------------------------------------------------------
    | Permission Gating (works with or without spatie/laravel-permission)
    |----------------------------------------------------------------------
    | When `enabled` is false, all authenticated users have full access.
    | When true, the DrivePermission middleware checks $user->can($ability).
    */
    'permissions' => [
        'enabled' => env('DRIVE_PERMISSIONS_ENABLED', true),
        'guard'   => env('DRIVE_PERMISSIONS_GUARD', 'web'),
        'abilities' => [
            'view'   => 'drive.files.view',
            'create' => 'drive.files.create',
            'delete' => 'drive.files.delete',
            'share'  => 'drive.files.share',
        ],
    ],
];
