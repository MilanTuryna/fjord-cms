<?php


namespace App\Model\Database\Repository\Admin;


use App\Model\Database\Repository;
use Nette\Database\Explorer;

/**
 * Class AccountRepository
 * @package App\Model\Database\Repository\Admin
 */
class AccountRepository extends Repository
{
    /**
     * AccountRepository constructor.
     * @param Explorer $explorer
     */
    public function __construct(Explorer $explorer)
    {
        parent::__construct("fjord_admin_account", $explorer);
    }
}