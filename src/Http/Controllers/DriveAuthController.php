<?php

namespace Janchris80\DriveFiles\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Janchris80\DriveFiles\Models\DriveToken;
use Janchris80\DriveFiles\Services\GoogleDriveService;

class DriveAuthController extends Controller
{
    public function __construct(private readonly GoogleDriveService $drive)
    {
    }

    public function redirect()
    {
        return redirect()->away($this->drive->getAuthUrl());
    }

    public function callback(Request $request): JsonResponse
    {
        $request->validate([
            'code' => ['required', 'string'],
        ]);

        if (! $request->user()) {
            abort(401);
        }

        $token = $this->drive->handleCallback(
            $request->input('code'),
            $request->user()
        );

        return response()->json([
            'message'    => 'Google Drive connected.',
            'expires_at' => $token->expires_at,
            'scope'      => $token->scope,
        ]);
    }

    public function status(Request $request): JsonResponse
    {
        $token = DriveToken::where('user_id', $request->user()->id)->first();

        return response()->json([
            'connected'  => (bool) $token,
            'expires_at' => $token?->expires_at,
            'scope'      => $token?->scope,
        ]);
    }

    public function disconnect(Request $request): JsonResponse
    {
        $this->drive->disconnect($request->user());

        return response()->json([
            'message' => 'Google Drive disconnected.',
        ]);
    }
}
