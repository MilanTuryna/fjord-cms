<?php

namespace App\Model\FileSystem\Gallery;

class GalleryDataProvider
{
    const URL_SEPARATOR = "/";

    /**
     * @var string $fullUrl starts with hostname (without separator on the end)
     */
    private string $fullUrl;

    /**
     * @param string $basePath
     * @param string $baseUrl
     */
    public function __construct(public string $basePath, public string $baseUrl) {
        $this->fullUrl = $this->baseUrl . self::URL_SEPARATOR . $this->basePath;
    }

    /**
     * @param string $galleryName
     * @param string $filename
     * @return string
     */
    public function getUrlToImage(string $galleryName, string $filename): string {
        return $this->fullUrl . self::URL_SEPARATOR . $galleryName . self::URL_SEPARATOR . $filename;
    }

    public function getFullUrl(): string {
        return $this->fullUrl;
    }
}