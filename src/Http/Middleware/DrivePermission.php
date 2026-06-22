<?php

namespace Janchris80\DriveFiles\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class DrivePermission
{
    public function handle(Request $request, Closure $next, string $ability)
    {
        if (! config('drive-files.permissions.enabled', true)) {
            return $next($request);
        }

        $permission = config('drive-files.permissions.abilities.'.$ability);

        if (! $permission) {
            abort(500, "Unknown drive ability: {$ability}");
        }

        $user = $request->user();

        if (! $user) {
            abort(401);
        }

        if (! $user->can($permission)) {
            abort(403, "Missing permission: {$permission}");
        }

        return $next($request);
    }
}
