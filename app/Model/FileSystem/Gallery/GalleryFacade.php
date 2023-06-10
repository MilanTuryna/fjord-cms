<?php

namespace App\Model\Facade;

use App\Model\Database\Repository\Gallery\Entity\Gallery;
use App\Model\Database\Repository\Gallery\Entity\GalleryItem;
use App\Model\Database\Repository\Gallery\GalleryRepository;
use App\Model\Database\Repository\Gallery\ItemsRepository;
use App\Model\FileSystem\Gallery\Objects\GalleryFileInfo;
use App\Model\FileSystem\Gallery\Objects\GalleryItemFile;
use App\Model\FileSystem\GalleryUploadManager;
use Nette\Database\Table\ActiveRow;
use ReflectionException;

class GalleryFacade
{
    private GalleryUploadManager $galleryUploadManager;

    public function __construct(public GalleryRepository $galleryRepository, public ItemsRepository $itemsRepository, public int $galleryId) {
        $this->galleryUploadManager = new GalleryUploadManager($this->galleryId);
    }

    /**
     * @throws ReflectionException
     */
    private function generateGalleryItemFile(ActiveRow $item, string $file_url): GalleryItemFile {
        $galleryItemFile = new GalleryItemFile();
        $galleryItemFile->createFrom($item);
        $galleryItemFile->file_url = "TODO"; // TODO
        return $galleryItemFile;
    }

    /**
     * @return GalleryItemFile[]
     * @throws ReflectionException
     */
    public function getItems(): array {
        $items = $this->itemsRepository->findByColumn(GalleryItem::gallery_id, $this->galleryId);
        $result = [];
        foreach ($items as $item) {
            $result[] = $this->generateGalleryItemFile($item, "TODO");
        }
        return $result;
    }

    /**
     * @throws ReflectionException
     */
    public function getGalleryItemFile(int $id): GalleryItemFile {
        return $this->generateGalleryItemFile($this->itemsRepository->findById($id), "TODO");
    }

    /**
     * @return GalleryFileInfo
     */
    public function getGalleryFileInfo(): GalleryFileInfo {
        return $this->galleryUploadManager->getGalleryFileInfo();
    }
}