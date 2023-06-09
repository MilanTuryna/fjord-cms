<?php

namespace App\Model\FileSystem\Gallery;

use App\Model\Database\Repository\Gallery\GalleryRepository;
use App\Model\Database\Repository\Gallery\ItemsRepository;
use App\Model\Facade\GalleryFacade;
use App\Model\FileSystem\GalleryUploadManager;


class GalleryFacadeFactory
{
    /**
     * @param ItemsRepository $itemsRepository
     * @param GalleryRepository $galleryRepository
     */
    public function __construct(private ItemsRepository $itemsRepository, private GalleryRepository $galleryRepository)
    {
    }

    /**
     * @param int $galleryId
     * @return GalleryFacade
     */
    public function getGalleryFacade(int $galleryId): GalleryFacade
    {
        return new GalleryFacade($this->galleryRepository, $this->itemsRepository, $galleryId);
    }
}