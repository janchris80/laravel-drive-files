<?php

namespace Janchris80\DriveFiles\Console\Commands;

use Illuminate\Console\Command;
use Janchris80\DriveFiles\Services\GoogleDriveService;

class DisconnectDriveCommand extends Command
{
    protected $signature = 'drive:disconnect';
    protected $description = 'Disconnect the application from Google Drive (removes stored token).';

    public function handle(GoogleDriveService $drive): int
    {
        if ($this->confirm('Disconnect the singleton Google Drive token?', true)) {
            $drive->disconnect();
            $this->info('✓ Disconnected.');
        }

        return self::SUCCESS;
    }
}
