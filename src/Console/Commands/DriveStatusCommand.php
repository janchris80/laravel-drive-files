<?php

namespace Janchris80\DriveFiles\Console\Commands;

use Illuminate\Console\Command;
use Janchris80\DriveFiles\Models\DriveToken;

class DriveStatusCommand extends Command
{
    protected $signature = 'drive:status';
    protected $description = 'Show the current Google Drive connection status.';

    public function handle(): int
    {
        $token = DriveToken::current();

        if (! $token) {
            $this->warn('Not connected. Run: php artisan drive:connect');
            return self::SUCCESS;
        }

        $this->info('✓ Connected');
        $this->line('  Account:     '.($token->connected_email ?? 'unknown'));
        $this->line('  Scope:       '.$token->scope);
        $this->line('  Expires at:  '.$token->expires_at);
        $this->line('  Has refresh: '.($token->refresh_token ? 'yes' : 'no'));

        return self::SUCCESS;
    }
}
