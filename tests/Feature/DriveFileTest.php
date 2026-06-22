<?php

namespace Janchris80\DriveFiles\Tests\Feature;

use Janchris80\DriveFiles\Tests\TestCase;

/**
 * TODO:
 *  - assert drive:connect prints an auth URL and persists DriveToken on valid code
 *  - assert drive:status reports "Not connected" when table is empty
 *  - assert /files/* endpoints fail with a clear error when no DriveToken exists
 *  - assert upload session validates mime/size against config
 *  - assert complete creates DB row + fires event
 *  - assert delete soft-deletes + calls Drive API
 *  - assert share only works when public_links_enabled = true
 *  - assert revoke clears the public_link
 *  - assert preview/download redirect to Drive URLs
 *  - assert auto-refresh of expired access tokens
 */
class DriveFileTest extends TestCase
{
    public function test_package_boots(): void
    {
        $this->assertTrue(true);
    }
}
