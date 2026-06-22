<?php

namespace Janchris80\DriveFiles\Tests\Feature;

use Janchris80\DriveFiles\Tests\TestCase;

/**
 * TODO:
 *  - assert auth required on every endpoint
 *  - assert list returns paginated results with filters
 *  - assert upload session validates mime/size against config
 *  - assert complete creates DB row + fires event
 *  - assert delete soft-deletes + calls Drive API
 *  - assert share only works when public_links_enabled = true
 *  - assert revoke clears the public_link
 *  - assert preview/download redirect to Drive URLs
 */
class DriveFileTest extends TestCase
{
    public function test_package_boots(): void
    {
        $this->assertTrue(true);
    }
}
