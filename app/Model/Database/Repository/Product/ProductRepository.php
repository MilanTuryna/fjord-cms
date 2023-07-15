<?php


namespace App\Model\Database\Repository\Product;


use App\Model\Database\Repository;
use JetBrains\PhpStorm\Pure;
use Nette\Database\Explorer;

/**
 * Class ProductRepository
 * @package App\Model\Database\Repository\Product
 */
class ProductRepository extends Repository
{
    #[Pure] public function __construct(Explorer $explorer)
    {
        parent::__construct("fjord_product", $explorer);
    }
}