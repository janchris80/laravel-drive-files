<?php

use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Support\Facades\Route;
use Janchris80\DriveFiles\Http\Controllers\DriveAuthController;
use Janchris80\DriveFiles\Http\Controllers\DriveFileController;

$prefix     = config('drive-files.routes.prefix', 'api/v1/drive');
$name       = config('drive-files.routes.name',   'api.v1.drive.');
$middleware = array_merge(
    config('drive-files.auth.middleware', []),
    config('drive-files.auth.extra_middleware', []),
    [SubstituteBindings::class],
);

Route::middleware($middleware)
    ->prefix($prefix)
    ->name($name)
    ->group(function () {
        // ---------------- OAuth (admin-only) ----------------
        Route::get('oauth/redirect', [DriveAuthController::class, 'redirect'])
            ->middleware('drive.permission:admin')
            ->name('oauth.redirect');

        Route::get('oauth/callback', [DriveAuthController::class, 'callback'])
            ->name('oauth.callback');

        Route::get('oauth/status', [DriveAuthController::class, 'status'])
            ->middleware('drive.permission:view')
            ->name('oauth.status');

        Route::delete('oauth/disconnect', [DriveAuthController::class, 'disconnect'])
            ->middleware('drive.permission:admin')
            ->name('oauth.disconnect');

        // ---------------- Files ----------------
        Route::get('files', [DriveFileController::class, 'index'])
            ->middleware('drive.permission:view')
            ->name('files.index');

        Route::post('files/upload-session', [DriveFileController::class, 'store'])
            ->middleware('drive.permission:create')
            ->name('files.upload-session');

        Route::post('files/complete', [DriveFileController::class, 'complete'])
            ->middleware('drive.permission:create')
            ->name('files.complete');

        Route::get('files/{driveFile}', [DriveFileController::class, 'show'])
            ->middleware('drive.permission:view')
            ->name('files.show');

        Route::get('files/{driveFile}/preview', [DriveFileController::class, 'preview'])
            ->middleware('drive.permission:view')
            ->name('files.preview');

        Route::get('files/{driveFile}/download', [DriveFileController::class, 'download'])
            ->middleware('drive.permission:view')
            ->name('files.download');

        Route::delete('files/{driveFile}', [DriveFileController::class, 'destroy'])
            ->middleware('drive.permission:delete')
            ->name('files.destroy');

        Route::post('files/{driveFile}/share', [DriveFileController::class, 'share'])
            ->middleware('drive.permission:share')
            ->name('files.share');

        Route::delete('files/{driveFile}/share', [DriveFileController::class, 'revokeShare'])
            ->middleware('drive.permission:share')
            ->name('files.revoke-share');
    });
