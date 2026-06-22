<?php

namespace Janchris80\DriveFiles;

use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;
use Janchris80\DriveFiles\Console\Commands\ConnectDriveCommand;
use Janchris80\DriveFiles\Console\Commands\DisconnectDriveCommand;
use Janchris80\DriveFiles\Console\Commands\DriveStatusCommand;
use Janchris80\DriveFiles\Contracts\DriveStorageInterface;
use Janchris80\DriveFiles\Http\Middleware\DrivePermission;
use Janchris80\DriveFiles\Services\GoogleDriveService;

class DriveFilesServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/drive-files.php', 'drive-files');

        $this->app->singleton(GoogleDriveService::class, function ($app) {
            return new GoogleDriveService(config('drive-files'));
        });

        $this->app->bind(DriveStorageInterface::class, GoogleDriveService::class);
    }

    public function boot(Router $router): void
    {
        $router->aliasMiddleware('drive.permission', DrivePermission::class);

        $this->publishes([
            __DIR__.'/../config/drive-files.php' => config_path('drive-files.php'),
        ], 'drive-files-config');

        $stampA = date('Y_m_d_His');
        $stampB = date('Y_m_d_His', time() + 1);

        $this->publishes([
            __DIR__.'/../database/migrations/2026_06_22_100000_create_drive_files_table.php'
                => database_path('migrations/'.$stampA.'_create_drive_files_table.php'),
            __DIR__.'/../database/migrations/2026_06_22_100001_create_drive_tokens_table.php'
                => database_path('migrations/'.$stampB.'_create_drive_tokens_table.php'),
        ], 'drive-files-migrations');

        $this->publishes([
            __DIR__.'/../database/seeders' => database_path('seeders'),
        ], 'drive-files-seeders');

        if (config('drive-files.auto_load_migrations', true)) {
            $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        }

        if (config('drive-files.routes.enabled', true)) {
            $this->loadRoutesFrom(__DIR__.'/../routes/drive.php');
        }

        if ($this->app->runningInConsole() && config('drive-files.commands.enabled', true)) {
            $this->commands([
                ConnectDriveCommand::class,
                DriveStatusCommand::class,
                DisconnectDriveCommand::class,
            ]);
        }
    }
}
