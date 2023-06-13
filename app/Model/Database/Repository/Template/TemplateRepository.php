<?php


namespace App\Model\Database\Repository\Template;


use App\Model\Database\Repository;
use Nette\Database\Explorer;

class TemplateRepository extends Repository
{
    public function __construct(Explorer $explorer)
    {
        parent::__construct("fjord_template", $explorer);
    }
}