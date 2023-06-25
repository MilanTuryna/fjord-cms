<?php


namespace App\Model\Database\Repository\Template;


use App\Model\Database\Repository;
use JetBrains\PhpStorm\Pure;
use Nette\Database\Explorer;

class PageRepository extends Repository
{
    /**
     * PageRepository constructor.
     * @param Explorer $explorer
     */
    #[Pure] public function __construct(Explorer $explorer)
    {
        parent::__construct("fjord_template_page", $explorer);
    }
}