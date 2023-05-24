<?php


namespace App\Model\Database\Repository\Dynamic;


use App\Model\Database\Repository;
use Nette\Database\Explorer;

class ValueRepository extends Repository
{
    /**
     * ValueRepository constructor.
     * @param Explorer $explorer
     */
    public function __construct(Explorer $explorer)
    {
        parent::__construct("fjord_dynamic_values", $explorer);
    }
}