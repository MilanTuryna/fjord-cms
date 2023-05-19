<?php


namespace App\Model\Database\Repository\Dynamic;


use App\Model\Database\AbstractRepository;
use Nette\Database\Explorer;

class EntityRepository extends AbstractRepository
{
    public function __construct(Explorer $explorer)
    {
        parent::__construct("fjord_dynamic_entity", $explorer);
    }
}