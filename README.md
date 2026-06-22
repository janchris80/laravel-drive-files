# laravel-drive-files

Google Drive file management for Laravel — **personal Google account support via OAuth 2.0**, resumable uploads, sharing, metadata storage, and optional permission gating.

> 🔑 This package is built for **personal Google accounts** (not Google Workspace). Each user connects their own Drive via OAuth 2.0; tokens are stored per-user. Shared Drives are **not** supported.

## Installation

```bash
composer require janchris80/laravel-drive-files
```

Publish config + migrations:

```bash
php artisan vendor:publish --tag=drive-files-config
php artisan migrate
```

## 1. Google Cloud Console Setup

1. Go to <https://console.cloud.google.com> → **APIs & Services → Library** → enable **Google Drive API**.
2. Go to **APIs & Services → Credentials** → **Create Credentials** → **OAuth client ID**.
3. Application type: **Web application**.
4. **Authorized redirect URIs**: add
   ```
   https://yourapp.test/api/v1/drive/oauth/callback
   ```
5. Copy the **Client ID** and **Client Secret** into `.env`.

> 🛡️ The package uses the `drive.file` scope by default. This means the app can only see and manage files **it created** — existing user files remain invisible. This is the safest scope for personal use and **does not require Google verification** for small apps.

## 2. Environment Variables

```env
GOOGLE_DRIVE_CLIENT_ID=
GOOGLE_DRIVE_CLIENT_SECRET=
GOOGLE_DRIVE_REDIRECT_URI=https://yourapp.test/api/v1/drive/oauth/callback
GOOGLE_DRIVE_APP_NAME="My App"
GOOGLE_DRIVE_PUBLIC_LINKS_ENABLED=false
GOOGLE_DRIVE_MAX_FILE_SIZE_MB=100

DRIVE_PERMISSIONS_ENABLED=true
DRIVE_ROUTES_ENABLED=true
DRIVE_ROUTES_PREFIX=api/v1/drive
```

## 3. OAuth Flow

```
┌──────────┐   1. GET /oauth/redirect     ┌────────────┐
│  Browser │ ───────────────────────────► │  Your App  │
└──────────┘                              └──────┬─────┘
                                                 │ 2. redirect to Google
                                                 ▼
                                          ┌────────────┐
                                          │   Google   │
                                          │  Consent   │
                                          └──────┬─────┘
                                                 │ 3. ?code=XYZ
                                                 ▼
                                          ┌────────────┐
                                          │ /callback  │ ──► persist DriveToken
                                          └────────────┘
```

Step-by-step:

1. Authenticated user opens `GET /api/v1/drive/oauth/redirect` → redirected to Google consent.
2. After consent, Google redirects to `/api/v1/drive/oauth/callback?code=...`.
3. The callback exchanges the code, stores the access + refresh token in `drive_tokens`, and returns JSON.
4. From now on, all `/files/*` endpoints work for that user.
5. `GET /api/v1/drive/oauth/status` reports whether the user is connected.
6. `DELETE /api/v1/drive/oauth/disconnect` revokes the local token.

The access token is **auto-refreshed** by `GoogleDriveService` using the stored `refresh_token` whenever it expires.

## 4. Permission Toggle

| `DRIVE_PERMISSIONS_ENABLED` | Behavior |
|---|---|
| `false` | Any authenticated user can use every endpoint |
| `true`  | `$user->can('drive.files.*')` is checked per route (works with Gates, Policies, or Spatie) |

Abilities (all configurable in `config/drive-files.php`):

- `drive.files.view`
- `drive.files.create`
- `drive.files.delete`
- `drive.files.share`
- `drive.files.connect`  ← required to initiate OAuth

## 5. Routes

| Method | URI | Ability |
|--------|-----|---------|
| GET    | `/api/v1/drive/oauth/redirect`                  | `connect` |
| GET    | `/api/v1/drive/oauth/callback`                  | (auth only) |
| GET    | `/api/v1/drive/oauth/status`                    | `view` |
| DELETE | `/api/v1/drive/oauth/disconnect`                | `connect` |
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

1. Client calls `POST /files/upload-session` with `filename`, `mime_type`, `size_bytes`, `origin`.
2. Server returns `{ "upload_uri": "https://www.googleapis.com/upload/drive/v3/files?..." }`.
3. Client `PUT`s the binary directly to that URI (no server bandwidth used).
4. On success, client calls `POST /files/complete` with the returned `google_file_id`.
5. Server fetches metadata, persists a `DriveFile` row, fires `DriveFileUploaded`.

## 7. Events

- `DriveFileUploaded`
- `DriveFileDeleted`
- `DriveFileShared`
- `DriveFileShareRevoked`

Each receives the `DriveFile` instance.

## 8. Customization

| File | Purpose |
|------|---------|
| `config/drive-files.php` | All settings — models, scopes, routes, permissions |
| `DriveFilePolicy` (publishable) | Per-record rules (owner-only, etc.) |
| `models.user` | Swap in a custom User model |

## License

MIT © Jungie Canghagas
