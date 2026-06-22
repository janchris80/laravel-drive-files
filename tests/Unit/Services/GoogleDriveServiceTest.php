<?php

namespace Janchris80\DriveFiles\Tests\Unit\Services;

use Janchris80\DriveFiles\Contracts\DriveStorageInterface;
use Janchris80\DriveFiles\Services\GoogleDriveService;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class GoogleDriveServiceTest extends TestCase
{
    public function test_implements_contract(): void
    {
        $ref = new ReflectionClass(GoogleDriveService::class);
        $this->assertTrue($ref->implementsInterface(DriveStorageInterface::class));
    }
}
