# laravel-drive-files

Google Drive file management for Laravel — resumable uploads, sharing, metadata storage, and **optional** permission gating.

## Installation

```bash
composer require janchris80/laravel-drive-files
```

Publish config, migrations, and seeders:

```bash
php artisan vendor:publish --tag=drive-files-config
php artisan vendor:publish --tag=drive-files-migrations
php artisan vendor:publish --tag=drive-files-seeders
php artisan migrate
```

## Configuration

Add to `.env`:

```env
GOOGLE_DRIVE_CREDENTIALS_PATH=storage/app/google/service-account.json
GOOGLE_DRIVE_SHARED_DRIVE_ID=
GOOGLE_DRIVE_ROOT_FOLDER_ID=
GOOGLE_DRIVE_PUBLIC_LINKS_ENABLED=false
GOOGLE_DRIVE_MAX_FILE_SIZE_MB=100

DRIVE_PERMISSIONS_ENABLED=true
DRIVE_ROUTES_ENABLED=true
DRIVE_ROUTES_PREFIX=api/v1/drive
```

### Toggling Permissions

The package works **with or without** a permission system.

| `DRIVE_PERMISSIONS_ENABLED` | Behavior |
|---|---|
| `false` | All authenticated users can use every endpoint |
| `true`  | `$user->can('drive.files.*')` is checked per route (works with Gates, Policies, or Spatie) |

## Routes

| Method | URI | Ability |
|--------|-----|---------|
| GET    | `/api/v1/drive/files`                          | `view`   |
| POST   | `/api/v1/drive/files/upload-session`           | `create` |
| POST   | `/api/v1/drive/files/complete`                 | `create` |
| GET    | `/api/v1/drive/files/{driveFile}`              | `view`   |
| GET    | `/api/v1/drive/files/{driveFile}/preview`      | `view`   |
| GET    | `/api/v1/drive/files/{driveFile}/download`     | `view`   |
| DELETE | `/api/v1/drive/files/{driveFile}`              | `delete` |
| POST   | `/api/v1/drive/files/{driveFile}/share`        | `share`  |
| DELETE | `/api/v1/drive/files/{driveFile}/share`        | `share`  |

## Events

- `DriveFileUploaded`
- `DriveFileDeleted`
- `DriveFileShared`
- `DriveFileShareRevoked`

## License

MIT © Jungie Canghagas
