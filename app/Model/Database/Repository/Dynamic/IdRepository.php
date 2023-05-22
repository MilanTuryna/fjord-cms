<?php


namespace App\Model\Database\Repository\Dynamic;


use App\Model\Database\Repository;
use Nette\Database\Explorer;

/**
 * Class IdRepository
 * @package App\Model\Database\Repository\Dynamic
 */
class IdRepository extends Repository
{
    /**
     * IdRepository constructor.
     * @param Explorer $explorer
     */
    public function __construct(Explorer $explorer)
    {
        parent::__construct("fjord_dynamic_ids", $explorer);
    }
}