<?php


namespace App\Model\Database\Repository\Gallery;


use App\Model\Database\Repository;
use Nette\Database\Explorer;

class GalleryRepository extends Repository
{
    public function __construct(Explorer $explorer)
    {
        parent::__construct("fjord_gallery", $explorer);
    }
}