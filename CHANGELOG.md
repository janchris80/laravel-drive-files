# Changelog

All notable changes to `laravel-drive-files` will be documented in this file.

## 0.3.0 - 2026-06-22

- **BREAKING:** single-tenant model. ONE Google Drive account services the entire app — perfect for personal projects and internal tools.
- `drive_tokens` table is now a **singleton** (no `user_id` column).
- Added `connected_email` column so you can see which Google account is bound.
- Removed `GoogleDriveService::forUser($user)` — service is now global.
- Actions no longer require a `$user` argument for Drive operations (but `CompleteUploadAction` still records `uploaded_by_user_id` for audit).
- Added Artisan commands: `drive:connect`, `drive:status`, `drive:disconnect`.
- Renamed `connect` ability to `admin` for the OAuth web flow.
- Web OAuth flow gated behind `drive.files.admin` permission.
- Auto-refresh of access tokens via the stored `refresh_token`.

## 0.2.0 - 2026-06-22

- Switched from Google service account to OAuth 2.0 for personal Google account support.
- Added `DriveToken` model + `drive_tokens` migration (per-user at the time).
- Added `DriveAuthController`.
- Removed `office_id` column.

## 0.1.0 - 2026-06-22

- Initial release. Service-account-based Google Drive integration.
- Resumable upload sessions, sharing, audit log support.
- Config-driven permission gating.
