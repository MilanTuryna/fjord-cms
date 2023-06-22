<?php

namespace App\Model\FileSystem\Gallery;

class GalleryDataProvider
{
    const URL_SEPARATOR = "/";

    /**
     * @param string $outsidePath
     * @param string $localPath
     */
    public function __construct(public string $outsidePath, public string $localPath) {
    }

    /**
     * @param string $galleryDirectory
     * @param string $filename
     * @return string
     */
    public function getUrlToImage(string $galleryDirectory, string $filename): string {
        return $this->outsidePath . self::URL_SEPARATOR . $galleryDirectory . self::URL_SEPARATOR . $filename;
    }
}