<?php


namespace App\Model\Database\Repository\Admin;


use App\Model\Database\AbstractRepository;
use Nette\Database\Explorer;

/**
 * Class AccountRepository
 * @package App\Model\Database\Repository\Admin
 */
class AccountRepository extends AbstractRepository
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