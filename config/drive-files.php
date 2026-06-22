<?php

return [

    /*
    |----------------------------------------------------------------------
    | Google OAuth 2.0 (Personal Google Accounts)
    |----------------------------------------------------------------------
    | Create an OAuth 2.0 Client ID in Google Cloud Console
    | (Application type: Web application) and copy the client ID +
    | client secret here. Add your callback URL to "Authorized
    | redirect URIs" in the same console.
    */
    'oauth' => [
        'client_id'     => env('GOOGLE_DRIVE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_DRIVE_CLIENT_SECRET'),
        'redirect_uri'  => env('GOOGLE_DRIVE_REDIRECT_URI'),
        'scopes'        => [
            // "drive.file" = the app can only see/manage files it created.
            // Safest scope for personal accounts and the only one that
            // doesn't require Google verification for small apps.
            'https://www.googleapis.com/auth/drive.file',
        ],
        'access_type' => 'offline',
        'prompt'      => 'consent',
    ],

    'application_name'     => env('GOOGLE_DRIVE_APP_NAME', 'Laravel Drive Files'),
    'root_folder_id'       => env('GOOGLE_DRIVE_ROOT_FOLDER_ID'),
    'public_links_enabled' => env('GOOGLE_DRIVE_PUBLIC_LINKS_ENABLED', false),

    /*
    |----------------------------------------------------------------------
    | Database
    |----------------------------------------------------------------------
    */
    'table_name'           => 'drive_files',
    'tokens_table_name'    => 'drive_tokens',
    'auto_load_migrations' => true,

    /*
    |----------------------------------------------------------------------
    | Host App Models
    |----------------------------------------------------------------------
    */
    'models' => [
        'user' => 'App\\Models\\User',
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
    */
    'permissions' => [
        'enabled' => env('DRIVE_PERMISSIONS_ENABLED', true),
        'guard'   => env('DRIVE_PERMISSIONS_GUARD', 'web'),
        'abilities' => [
            'view'    => 'drive.files.view',
            'create'  => 'drive.files.create',
            'delete'  => 'drive.files.delete',
            'share'   => 'drive.files.share',
            'connect' => 'drive.files.connect',
        ],
    ],
];
