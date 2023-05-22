<?php


namespace App\Model\Database\Repository\Admin;


use App\Model\Database\Repository;
use Nette\Database\Explorer;

/**
 * Class AccessLogRepository
 * @package App\Model\Database\Repository\Admin
 */
class AccessLogRepository extends Repository
{
    /**
     * AccessLogRepository constructor.
     * @param Explorer $explorer
     */
    public function __construct(Explorer $explorer)
    {
        parent::__construct("fjord_admin_accesslog", $explorer);
    }
}