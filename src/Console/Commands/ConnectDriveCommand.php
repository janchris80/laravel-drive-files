<?php

namespace Janchris80\DriveFiles\Console\Commands;

use Illuminate\Console\Command;
use Janchris80\DriveFiles\Services\GoogleDriveService;
use Throwable;

class ConnectDriveCommand extends Command
{
    protected $signature = 'drive:connect';
    protected $description = 'Connect the application to a personal Google Drive account.';

    public function handle(GoogleDriveService $drive): int
    {
        $this->info('Open the following URL in a browser, authorize the app, and paste the code below:');
        $this->line('');
        $this->line($drive->getAuthUrl());
        $this->line('');

        $code = $this->ask('Authorization code');

        if (! $code) {
            $this->error('No code provided.');
            return self::FAILURE;
        }

        try {
            $token = $drive->handleCallback($code);
            $this->info('✓ Google Drive connected.');
            if ($token->connected_email) {
                $this->line('  Account:    '.$token->connected_email);
            }
            $this->line('  Scope:      '.$token->scope);
            $this->line('  Expires at: '.$token->expires_at);
            return self::SUCCESS;
        } catch (Throwable $e) {
            $this->error('Failed: '.$e->getMessage());
            return self::FAILURE;
        }
    }
}
