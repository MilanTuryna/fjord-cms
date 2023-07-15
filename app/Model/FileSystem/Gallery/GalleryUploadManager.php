<?php

namespace App\Model\FileSystem\Gallery;

use App\Model\FileSystem\FileUtils;
use App\Model\FileSystem\Gallery\Exceptions\GalleryUniqueException;
use App\Model\FileSystem\Gallery\Exceptions\RenameGalleryFailedException;
use App\Model\FileSystem\Gallery\GalleryDataProvider;
use App\Model\FileSystem\Gallery\Objects\GalleryFileInfo;
use App\Model\FileSystem\UploadManager;
use FilesystemIterator;
use JetBrains\PhpStorm\Pure;

/**
 * Class GalleryUploadManager
 * @package App\Model\FileSystem\Gallery
 */
class GalleryUploadManager extends UploadManager
{
    const VIDEO_EXTENSIONS = ["mp4", "webm"];
    const IMAGE_EXTENSIONS = ["jpg", "png", "gif", "webp", "jpeg",  "bmp"];

    const ALLOWED_EXTENSIONS = [...self::IMAGE_EXTENSIONS, ...self::VIDEO_EXTENSIONS];

    const VIDEO_FRAME_FORMAT = "jpg";


    /**
     * @param GalleryDataProvider $galleryDataProvider
     * @param string $galleryDirectory
     */
    #[Pure] public function __construct(private GalleryDataProvider $galleryDataProvider, private string $galleryDirectory)
    {
        parent::__construct($galleryDataProvider->localPath . DIRECTORY_SEPARATOR . $galleryDirectory, self::ALLOWED_EXTENSIONS);
    }

    /**
     * @return GalleryFileInfo
     */
    public function getGalleryFileInfo(): GalleryFileInfo {
        $galleryFileInfo = new GalleryFileInfo();
        $path = $this->path . DIRECTORY_SEPARATOR;
        if(!is_dir($path)) mkdir($path);
        $fi = new FilesystemIterator($path, FilesystemIterator::SKIP_DOTS);
        $globalFileSize = 0;
        foreach ($fi as $file) {
            $globalFileSize += $file->getSize();
        }
        $galleryFileInfo->file_count = iterator_count($fi);
        $galleryFileInfo->raw_size = $globalFileSize;
        return $galleryFileInfo;
    }

    public function getVideoFrame(string $compressedVideoName): string
    {
        $framesFolder = $this->path . DIRECTORY_SEPARATOR . "frames";
        if(!file_exists($framesFolder)) mkdir($framesFolder);
        return $framesFolder. DIRECTORY_SEPARATOR . $compressedVideoName . "." . self::VIDEO_FRAME_FORMAT;
    }

    /**
     * @param string $compressedVideoName
     * @return bool
     */
    public function removeVideoFrame(string $compressedVideoName): bool {
        $frame = $this->getVideoFrame($compressedVideoName);
        if(!file_exists($frame)) return false;
        return unlink($this->getVideoFrame($compressedVideoName));
    }

    /**
     * @param string $filename
     * @return bool
     */
    public static function isVideo(string $filename): bool {
        return in_array(FileUtils::getExtension($filename), self::VIDEO_EXTENSIONS);
    }

    public function getDirectoryName(): string {
        return $this->galleryDirectory;
    }

    /**
     * non used but for future prepare (now we are using ids)
     */
    public function renameGallery($galleryDirectoryFrom, $galleryDirectoryTo): void {
        $from = $this->galleryDataProvider->localPath . DIRECTORY_SEPARATOR . $galleryDirectoryFrom;
        $to = $this->galleryDataProvider->localPath . DIRECTORY_SEPARATOR . $galleryDirectoryTo;
        $this->renameDirectory($from, $to);
    }
}