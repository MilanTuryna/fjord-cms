<?php


namespace App\Model\Database\Repository\SMTP;


use App\Model\Database\Repository;
use JetBrains\PhpStorm\Pure;
use Nette\Database\Explorer;

/**
 * Class MailRepository
 * @package App\Model\Database\Repository\SMTP
 */
class MailRepository extends Repository
{
    /**
     * MailRepository constructor.
     * @param Explorer $explorer
     */
    #[Pure] public function __construct(Explorer $explorer)
    {
        parent::__construct("fjord_smtp_mails", $explorer);
    }
}