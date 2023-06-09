<?php

namespace App\Model\FileSystem;

use App\Model\FileSystem\Gallery\Objects\GalleryFileInfo;

class GalleryUploadManager extends UploadManager
{
    const ALLOWED_EXTENSIONS = ["jpg", "png", "gif", "webp", "jpeg", "bmp"];

    /**
     * @param string $galleryId
     */
    public function __construct(string $galleryId)
    {
        parent::__construct("/galleries/" . $galleryId . "/", self::ALLOWED_EXTENSIONS);
    }

    /**
     * @return GalleryFileInfo
     */
    public function getGalleryFileInfo(): GalleryFileInfo {
        $galleryFileInfo = new GalleryFileInfo();
        $glob = glob($this->path . "*.{" . implode(",", self::ALLOWED_EXTENSIONS) . "}");
        $globalFileSize = 0;
        foreach ($glob as $fileName) {
            $globalFileSize += filesize($fileName);
        }
        $galleryFileInfo->file_count = count($glob);
        $galleryFileInfo->raw_size = $globalFileSize;
        return $galleryFileInfo;
    }
}