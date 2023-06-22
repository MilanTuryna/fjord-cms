<?php

namespace App\Model\FileSystem\Gallery;

use App\Model\Database\Repository\Gallery\Entity\Gallery;
use App\Model\Database\Repository\Gallery\Entity\GalleryItem;
use App\Model\Database\Repository\Gallery\GalleryRepository;
use App\Model\Database\Repository\Gallery\ItemsRepository;
use App\Model\FileSystem\Gallery\Exceptions\ImageNotExistException;
use App\Model\FileSystem\Gallery\Objects\GalleryFileInfo;
use App\Model\FileSystem\Gallery\Objects\GalleryItemFile;
use JetBrains\PhpStorm\Pure;
use Nette\Database\Table\ActiveRow;
use ReflectionException;
use Tracy\Debugger;

class GalleryFacade
{
    private GalleryUploadManager $galleryUploadManager;

    /**
     * @param GalleryRepository $galleryRepository
     * @param GalleryDataProvider $galleryDataProvider
     * @param ItemsRepository $itemsRepository
     * @param int $galleryId
     */
    public function __construct(public GalleryRepository $galleryRepository, public GalleryDataProvider $galleryDataProvider, public ItemsRepository $itemsRepository, public int $galleryId) {
        $this->galleryUploadManager = new GalleryUploadManager($this->galleryDataProvider, $this->galleryId);
    }

    /**
     * @throws ReflectionException
     */
    private function generateGalleryItemFile(ActiveRow $item, string $fileUrl): GalleryItemFile {
        $galleryItemFile = new GalleryItemFile();
        $galleryItemFile->createFrom((object)$item->toArray(), false, true);
        $galleryItemFile->file_url = $fileUrl;
        return $galleryItemFile;
    }

    /**
     * @return GalleryItemFile[]
     * @throws ReflectionException|ImageNotExistException
     */
    public function getItems(?int $limit = null): array {
        $items = $this->itemsRepository->findByColumn(GalleryItem::gallery_id, $this->galleryId);
        if($limit) $items = $items->limit($limit);
        $items = $items->fetchAll();
        /**
         * @var $gallery Gallery
         */
        $gallery = $this->galleryRepository->findById($this->galleryId);
        $result = [];
        /**
         * @var $items GalleryItem[]|ActiveRow[]
         */
        foreach ($items as $item) {
            try {
                $result[] = $this->generateGalleryItemFile($item, $this->galleryDataProvider->getUrlToImage($gallery->id, $item->compressed_file));
            } catch (ImageNotExistException $imageNotExistException) {
                $this->itemsRepository->deleteById($item->id);
                if(Debugger::$productionMode === false) {
                    throw $imageNotExistException;
                }
            }
        }
        return $result;
    }

    /**
     * @throws ReflectionException|ImageNotExistException
     */
    public function getGalleryItemFile(int $id): GalleryItemFile {
        $gallery = $this->galleryRepository->findById($this->galleryId);
        $item = $this->itemsRepository->findById($id);
        if(!$this->galleryUploadManager->isFileExist($item->compressed_file)) {
            throw new ImageNotExistException();
        }
        return $this->generateGalleryItemFile($item,  $this->galleryDataProvider->getUrlToImage($gallery->name, $item->compressed_file));
    }

    /**
     * @return GalleryUploadManager
     */
    public function getGalleryUploadManager(): GalleryUploadManager {
        return $this->galleryUploadManager;
    }

    /**
     * @return GalleryFileInfo
     */
    #[Pure] public function getGalleryFileInfo(): GalleryFileInfo {
        return $this->galleryUploadManager->getGalleryFileInfo();
    }
}