# laravel-drive-files — single-tenant edition

Google Drive integration for Laravel. **One personal Google account is connected once by the app owner**; all users of the app upload to that Drive. Resumable uploads, sharing, metadata, and optional permission gating.

> 🎯 Built for personal projects, internal tools, LGU/org apps, and any case where uploads should land in **one central Drive** — not per-user accounts.

## Installation

```bash
composer require janchris80/laravel-drive-files
php artisan vendor:publish --tag=drive-files-config
php artisan migrate
```

## 1. Google Cloud Console Setup

1. <https://console.cloud.google.com> → **APIs & Services → Library** → enable **Google Drive API**.
2. **Credentials → Create Credentials → OAuth client ID**.
3. Application type: **Web application**.
4. **Authorized redirect URIs**: add
   ```
   https://yourapp.test/api/v1/drive/oauth/callback
   ```
5. Copy **Client ID** and **Client Secret** into `.env`.

> 🛡 Default scope is `drive.file` — the app can only see/manage files **it created**. Safest for personal accounts and **no Google verification required**.

## 2. Environment Variables

```env
GOOGLE_DRIVE_CLIENT_ID=
GOOGLE_DRIVE_CLIENT_SECRET=
GOOGLE_DRIVE_REDIRECT_URI=https://yourapp.test/api/v1/drive/oauth/callback
GOOGLE_DRIVE_APP_NAME="My App"
GOOGLE_DRIVE_ROOT_FOLDER_ID=
GOOGLE_DRIVE_PUBLIC_LINKS_ENABLED=false

DRIVE_PERMISSIONS_ENABLED=true
DRIVE_ROUTES_ENABLED=true
DRIVE_COMMANDS_ENABLED=true
DRIVE_ROUTES_PREFIX=api/v1/drive
```

## 3. One-Time Connection — TWO WAYS

### Method A — Artisan (recommended, fully headless)

```bash
php artisan drive:connect
```

Open the URL the command prints, authorize, then paste the code back into the terminal. Done — token stored.

```bash
php artisan drive:status      # check the connection
php artisan drive:disconnect  # remove the token
```

### Method B — Web flow (for admin panels)

A user with the **`drive.files.admin`** permission visits:

```
GET /api/v1/drive/oauth/redirect
```

They're sent to Google's consent screen. After approval Google calls back to:

```
GET /api/v1/drive/oauth/callback?code=...
```

…which stores the token and returns JSON.

Either way, the result is the same single row in `drive_tokens`. All `/files/*` endpoints then work for **all app users** (subject to their own per-user permissions).

## 4. Permission Toggle

| `DRIVE_PERMISSIONS_ENABLED` | Behavior |
|---|---|
| `false` | Any authenticated user can use every endpoint |
| `true`  | `$user->can('drive.files.*')` is checked per route |

Abilities:

- `drive.files.view`
- `drive.files.create`
- `drive.files.delete`
- `drive.files.share`
- `drive.files.admin` ← required to connect/disconnect via web

## 5. Routes

| Method | URI | Ability |
|--------|-----|---------|
| GET    | `/api/v1/drive/oauth/redirect`                  | `admin` |
| GET    | `/api/v1/drive/oauth/callback`                  | (auth only) |
| GET    | `/api/v1/drive/oauth/status`                    | `view` |
| DELETE | `/api/v1/drive/oauth/disconnect`                | `admin` |
| GET    | `/api/v1/drive/files`                           | `view` |
| POST   | `/api/v1/drive/files/upload-session`            | `create` |
| POST   | `/api/v1/drive/files/complete`                  | `create` |
| GET    | `/api/v1/drive/files/{driveFile}`               | `view` |
| GET    | `/api/v1/drive/files/{driveFile}/preview`       | `view` |
| GET    | `/api/v1/drive/files/{driveFile}/download`      | `view` |
| DELETE | `/api/v1/drive/files/{driveFile}`               | `delete` |
| POST   | `/api/v1/drive/files/{driveFile}/share`         | `share` |
| DELETE | `/api/v1/drive/files/{driveFile}/share`         | `share` |

## 6. Resumable Upload Flow

1. Client → `POST /files/upload-session` with `filename`, `mime_type`, `size_bytes`, `origin`.
2. Server returns `{ "upload_uri": "https://..." }`.
3. Client `PUT`s the binary directly to that URI (no server bandwidth).
4. Client → `POST /files/complete` with the returned `google_file_id`.
5. Server persists metadata, fires `DriveFileUploaded`.

## 7. Events

- `DriveFileUploaded`
- `DriveFileDeleted`
- `DriveFileShared`
- `DriveFileShareRevoked`

Each receives the `DriveFile` instance.

## 8. Auto-Refresh

The package auto-refreshes the access token using the stored `refresh_token`. You **don't need to reconnect** unless you explicitly disconnect or Google revokes the grant (e.g. you change your password, or the app is removed from your Google account permissions).

## 9. Customization

| File | Purpose |
|------|---------|
| `config/drive-files.php` | All settings — models, scopes, routes, permissions, commands |
| `DriveFilePolicy` | Per-record rules (owner-only, etc.) |
| `models.user` | Swap in a custom User model |

## License

MIT © Jan Chris Ogel
