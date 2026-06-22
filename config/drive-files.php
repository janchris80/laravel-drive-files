<?php

return [

    /*
    |----------------------------------------------------------------------
    | Google OAuth 2.0 (Single Personal Google Account)
    |----------------------------------------------------------------------
    | The app owner connects ONE personal Google Drive account once.
    | All app users upload to this same Drive.
    */
    'oauth' => [
        'client_id'     => env('GOOGLE_DRIVE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_DRIVE_CLIENT_SECRET'),
        'redirect_uri'  => env('GOOGLE_DRIVE_REDIRECT_URI'),
        'scopes'        => [
            'https://www.googleapis.com/auth/drive.file',
            'https://www.googleapis.com/auth/userinfo.email',
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
    | Routes / Auth / Commands
    |----------------------------------------------------------------------
    */
    'routes' => [
        'enabled' => env('DRIVE_ROUTES_ENABLED', true),
        'prefix'  => env('DRIVE_ROUTES_PREFIX', 'api/v1/drive'),
        'name'    => env('DRIVE_ROUTES_NAME', 'api.v1.drive.'),
    ],

    'auth' => [
        'middleware'       => ['auth:sanctum'],
        'extra_middleware' => [],
    ],

    'commands' => [
        'enabled' => env('DRIVE_COMMANDS_ENABLED', true),
    ],

    /*
    |----------------------------------------------------------------------
    | Permission Gating
    |----------------------------------------------------------------------
    */
    'permissions' => [
        'enabled' => env('DRIVE_PERMISSIONS_ENABLED', true),
        'guard'   => env('DRIVE_PERMISSIONS_GUARD', 'web'),
        'abilities' => [
            'view'   => 'drive.files.view',
            'create' => 'drive.files.create',
            'delete' => 'drive.files.delete',
            'share'  => 'drive.files.share',
            'admin'  => 'drive.files.admin',
        ],
    ],
];
