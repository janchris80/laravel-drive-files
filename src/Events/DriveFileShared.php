<?php

namespace Janchris80\DriveFiles\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Janchris80\DriveFiles\Models\DriveFile;

class DriveFileShared
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(public DriveFile $file)
    {
    }
}
