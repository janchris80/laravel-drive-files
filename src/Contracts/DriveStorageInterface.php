<?php

namespace Janchris80\DriveFiles\Contracts;

interface DriveStorageInterface
{
    public function createResumableSession(array $params): array;
    public function getFileInfo(string $id): array;
    public function deleteFile(string $id): bool;
    public function createPublicPermission(string $id): string;
    public function removePublicPermission(string $id): bool;
    public function getTemporaryUrl(string $id, int $ttl = 3600): string;
    public function getPreviewUrl(string $id): string;
}
