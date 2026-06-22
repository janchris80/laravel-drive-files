<?php

namespace Janchris80\DriveFiles\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DriveToken extends Model
{
    protected $fillable = [
        'user_id',
        'access_token',
        'refresh_token',
        'expires_at',
        'scope',
        'token_type',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    public function getTable(): string
    {
        return config('drive-files.tokens_table_name', 'drive_tokens');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(config('drive-files.models.user'), 'user_id');
    }

    public function isExpired(): bool
    {
        return $this->expires_at !== null && $this->expires_at->isPast();
    }

    /**
     * Format for Google\Client::setAccessToken().
     */
    public function toGoogleArray(): array
    {
        $expiresIn = 3600;
        if ($this->expires_at instanceof Carbon) {
            $expiresIn = max(0, now()->diffInSeconds($this->expires_at, false));
        }

        return [
            'access_token'  => $this->access_token,
            'refresh_token' => $this->refresh_token,
            'expires_in'    => $expiresIn,
            'created'       => optional($this->updated_at)->timestamp ?? time(),
            'token_type'    => $this->token_type ?? 'Bearer',
            'scope'         => $this->scope,
        ];
    }
}
