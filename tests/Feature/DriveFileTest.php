<?php

namespace Janchris80\DriveFiles\Tests\Feature;

use Janchris80\DriveFiles\Tests\TestCase;

/**
 * TODO:
 *  - assert OAuth redirect returns a Google URL
 *  - assert callback persists DriveToken row
 *  - assert /files/* endpoints require a connected user (DriveToken exists)
 *  - assert disconnect deletes DriveToken
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
