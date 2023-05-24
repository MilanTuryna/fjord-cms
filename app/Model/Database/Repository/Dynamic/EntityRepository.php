<?php


namespace App\Model\Database\Repository\Dynamic;


use App\Model\Database\Repository;
use Nette\Database\Explorer;

class EntityRepository extends Repository
{
    public function __construct(Explorer $explorer)
    {
        parent::__construct("fjord_dynamic_entity", $explorer);
    }
}