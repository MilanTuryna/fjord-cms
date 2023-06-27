<?php

namespace App\Model\FileSystem\Gallery;

use App\Model\Database\Repository\Gallery\GalleryRepository;
use App\Model\Database\Repository\Gallery\ItemsRepository;
use App\Model\FileSystem\Gallery\GalleryFacade;
use JetBrains\PhpStorm\Pure;

/**
 * Class GalleryFacadeFactory
 * @package App\Model\FileSystem\Gallery
 */
class GalleryFacadeFactory
{
    /**
     * @param ItemsRepository $itemsRepository
     * @param GalleryDataProvider $galleryDataProvider
     * @param GalleryRepository $galleryRepository
     */
    public function __construct(private ItemsRepository $itemsRepository, private GalleryDataProvider $galleryDataProvider, private GalleryRepository $galleryRepository)
    {
    }

    /**
     * @param int $galleryId
     * @return GalleryFacade
     */
    #[Pure] public function getGalleryFacade(int $galleryId): GalleryFacade
    {
        return new GalleryFacade($this->galleryRepository, $this->galleryDataProvider, $this->itemsRepository, $galleryId);
    }

    /**
     * @return GalleryRepository
     */
    public function getGalleryRepository(): GalleryRepository {
        return $this->galleryRepository;
    }
}