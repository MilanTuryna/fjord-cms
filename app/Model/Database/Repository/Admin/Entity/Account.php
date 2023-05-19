<?php


namespace App\Model\Database\Repository\Admin\Entity;


use App\Model\Database\Entity;

/**
 * Class Account
 * @package App\Model\Database\Repository\Admin\Entity
 */
class Account extends Entity
{
    public string $password;
    public string $email;
    public string $username;
}