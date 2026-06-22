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

        $token = $this->drive->handleCallback($request->input('code'));

        return response()->json([
            'message'         => 'Google Drive connected.',
            'connected_email' => $token->connected_email,
            'expires_at'      => $token->expires_at,
            'scope'           => $token->scope,
        ]);
    }

    public function status(): JsonResponse
    {
        $token = DriveToken::current();

        return response()->json([
            'connected'         => (bool) $token,
            'connected_email'   => $token?->connected_email,
            'expires_at'        => $token?->expires_at,
            'scope'             => $token?->scope,
            'has_refresh_token' => (bool) $token?->refresh_token,
        ]);
    }

    public function disconnect(): JsonResponse
    {
        $this->drive->disconnect();

        return response()->json([
            'message' => 'Google Drive disconnected.',
        ]);
    }
}
