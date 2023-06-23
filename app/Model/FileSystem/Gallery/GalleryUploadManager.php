<?php

namespace App\Model\FileSystem\Gallery;

use App\Model\FileSystem\Gallery\Exceptions\GalleryUniqueException;
use App\Model\FileSystem\Gallery\Exceptions\RenameGalleryFailedException;
use App\Model\FileSystem\Gallery\GalleryDataProvider;
use App\Model\FileSystem\Gallery\Objects\GalleryFileInfo;
use App\Model\FileSystem\UploadManager;
use FilesystemIterator;
use JetBrains\PhpStorm\Pure;

class GalleryUploadManager extends UploadManager
{
    const ALLOWED_EXTENSIONS = ["jpg", "png", "gif", "webp", "jpeg", "bmp"];


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
        $fi = new FilesystemIterator($path, FilesystemIterator::SKIP_DOTS);
        $globalFileSize = 0;
        foreach ($fi as $file) {
            $globalFileSize += $file->getSize();
        }
        $galleryFileInfo->file_count = iterator_count($fi);
        $galleryFileInfo->raw_size = $globalFileSize;
        return $galleryFileInfo;
    }

    public function getDirectoryName(): string {
        return $this->galleryDirectory;
    }

    /**
     * @throws GalleryUniqueException
     * @throws RenameGalleryFailedException
     * non used but for future prepare (now we are using ids)
     */
    public function renameGallery($galleryDirectoryFrom, $galleryDirectoryTo): void {
        $from = $this->galleryDataProvider->localPath . DIRECTORY_SEPARATOR . $galleryDirectoryFrom;
        $to = $this->galleryDataProvider->localPath . DIRECTORY_SEPARATOR . $galleryDirectoryTo;
        if(file_exists($to)) throw new GalleryUniqueException();
        if(!rename($from, $to)) throw new RenameGalleryFailedException();
    }
}