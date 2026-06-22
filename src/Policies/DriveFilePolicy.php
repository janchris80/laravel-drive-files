<?php

namespace Janchris80\DriveFiles\Policies;

use Janchris80\DriveFiles\Models\DriveFile;

/**
 * Default policy is permissive — gating is handled by the
 * DrivePermission middleware. Override these methods in the
 * host app to add per-record rules (e.g. "owner only").
 */
class DriveFilePolicy
{
    public function viewAny($user): bool { return true; }
    public function view($user, DriveFile $file): bool { return true; }
    public function create($user): bool { return true; }
    public function update($user, DriveFile $file): bool { return true; }
    public function delete($user, DriveFile $file): bool { return true; }
    public function share($user, DriveFile $file): bool { return true; }
}
