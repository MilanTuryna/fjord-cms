<?php

namespace App\Model\FileSystem\Gallery\Objects;

use App\Model\Database\Repository\Gallery\Entity\GalleryItem;
use App\Model\FileSystem\Gallery\GalleryUploadManager;

/**
 * Class GalleryItemFile
 * @package App\Model\FileSystem\Gallery\Objects
 */
class GalleryItemFile extends GalleryItem
{
    public string $file_url;
    public string $file_path;
    public ?string $video_frame_path;

    /**
     * @return bool
     */
    public function isVideo(): bool {
        return GalleryUploadManager::isVideo($this->compressed_file);
    }
}