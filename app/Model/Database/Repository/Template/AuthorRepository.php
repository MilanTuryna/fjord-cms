<?php


namespace App\Model\Database\Repository\Template;


use App\Model\Database\Repository;
use Nette\Database\Explorer;

class AuthorRepository extends Repository
{
    public function __construct(Explorer $explorer)
    {
        parent::__construct("fjord_template_author", $explorer);
    }
}