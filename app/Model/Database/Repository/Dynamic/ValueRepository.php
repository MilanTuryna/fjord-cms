<?php


namespace App\Model\Database\Repository\Dynamic;


use App\Model\Database\AbstractRepository;
use Nette\Database\Explorer;

class ValueRepository extends AbstractRepository
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