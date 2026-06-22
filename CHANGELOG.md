# Changelog

All notable changes to `laravel-drive-files` will be documented in this file.

## 0.2.0 - 2026-06-22

- **BREAKING:** switched from Google service account to **OAuth 2.0** for personal Google account support.
- Added `DriveToken` model + `drive_tokens` migration to persist per-user access + refresh tokens.
- Added `DriveAuthController` with `redirect` / `callback` / `status` / `disconnect` endpoints.
- Added `connect` ability under `drive-files.permissions.abilities`.
- Removed `office_id` column and `Office` model relationship (personal-use focus).
- `GoogleDriveService` is now user-scoped via `forUser($user)`.
- Automatic access token refresh using the stored `refresh_token`.
- Removed `supportsAllDrives` flag from all API calls (Shared Drives are Workspace-only).
- All Actions now require a `$user` argument so they can resolve the right token.

## 0.1.0 - 2026-06-22

- Initial release.
- Service-account-based Google Drive integration.
- Resumable upload sessions.
- File sharing (public link) with config toggle.
- DriveFile model with optional spatie/laravel-activitylog.
- Config-driven permission gating (works with or without spatie/laravel-permission).
- Events: Uploaded, Deleted, Shared, ShareRevoked.
- Policies and middleware.
- Orchestra Testbench test scaffolding.
