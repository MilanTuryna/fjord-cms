<?php


namespace App\Model\FileSystem\Admin;

/**
 * Class AdminUploadDataProvider
 * @package App\Model\FileSystem\Admin
 */
class AdminUploadDataProvider
{
    public function __construct(public string $outsidePath, public string $localPath) {
    }
}