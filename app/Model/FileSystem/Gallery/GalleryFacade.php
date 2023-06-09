<?php

namespace App\Model\Facade;

use App\Model\Database\Repository\Gallery\Entity\Gallery;
use App\Model\Database\Repository\Gallery\Entity\GalleryItem;
use App\Model\Database\Repository\Gallery\GalleryRepository;
use App\Model\Database\Repository\Gallery\ItemsRepository;
use App\Model\FileSystem\Gallery\Objects\GalleryFileInfo;
use App\Model\FileSystem\Gallery\Objects\GalleryItemFile;
use App\Model\FileSystem\GalleryUploadManager;
use ReflectionException;

class GalleryFacade
{
    private GalleryUploadManager $galleryUploadManager;

    public function __construct(public GalleryRepository $galleryRepository, public ItemsRepository $itemsRepository, public int $galleryId) {
        $this->galleryUploadManager = new GalleryUploadManager($this->galleryId);
    }

    /**
     * @return GalleryItemFile[]
     * @throws ReflectionException
     */
    public function getItems(): array {
        $items = $this->itemsRepository->findByColumn(GalleryItem::gallery_id, $this->galleryId);
        $result = [];
        foreach ($items as $item) {
            $galleryItemFile = new GalleryItemFile();
            $galleryItemFile->createFrom($item);
            $galleryItemFile->file_url = "TODO";
            $result[] = $galleryItemFile;
        }
        return $result;
    }

    /**
     * @return GalleryFileInfo
     */
    public function getGalleryFileInfo(): GalleryFileInfo {
        return $this->galleryUploadManager->getGalleryFileInfo();
    }
}