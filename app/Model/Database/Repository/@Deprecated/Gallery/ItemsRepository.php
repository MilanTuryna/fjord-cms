<?php


namespace App\Model\Database\Repository\Gallery;


use App\Model\Database\Repository;
use JetBrains\PhpStorm\Pure;
use Nette\Database\Explorer;

class ItemsRepository extends Repository
{
    #[Pure] public function __construct(Explorer $explorer)
    {
        parent::__construct("fjord_gallery_item", $explorer);
    }
}