<?php

namespace Janchris80\DriveFiles\Database\Seeders;

use Illuminate\Database\Seeder;

class DrivePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissionClass = '\\Spatie\\Permission\\Models\\Permission';
        $roleClass       = '\\Spatie\\Permission\\Models\\Role';

        if (! class_exists($permissionClass) || ! class_exists($roleClass)) {
            $this->command?->warn(
                'spatie/laravel-permission is not installed. Skipping DrivePermissionSeeder.'
            );
            return;
        }

        $guard       = config('drive-files.permissions.guard', 'web');
        $abilities   = config('drive-files.permissions.abilities', []);

        foreach ($abilities as $key => $name) {
            $permissionClass::firstOrCreate(
                ['name' => $name, 'guard_name' => $guard]
            );
        }

        $admin  = $roleClass::firstOrCreate(['name' => 'drive-admin',  'guard_name' => $guard]);
        $viewer = $roleClass::firstOrCreate(['name' => 'drive-viewer', 'guard_name' => $guard]);

        $admin->syncPermissions(array_values($abilities));
        $viewer->syncPermissions([$abilities['view'] ?? 'drive.files.view']);
    }
}
