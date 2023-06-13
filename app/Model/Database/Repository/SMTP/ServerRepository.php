<?php


namespace App\Model\Database\Repository\SMTP;


use JetBrains\PhpStorm\Pure;
use Nette\Database\Explorer;

/**
 * Class ServerRepository
 * @package App\Model\Database\Repository\SMTP
 */
class ServerRepository extends \App\Model\Database\Repository
{
    #[Pure] public function __construct(Explorer $explorer)
    {
        parent::__construct("fjord_smtp_servers", $explorer);
    }
}