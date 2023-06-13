<?php


namespace App\Model\Database\Repository\Template\Entity;


use App\Model\Database\Entity;

/**
 * Class Author
 * @package App\Model\Database\Repository\Template\Entity
 */
class Author extends Entity
{
    const name = "name", email = "email", website = "website";

    public string $name;
    public string $email;
    public string $website;
}