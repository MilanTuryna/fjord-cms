<?php


namespace App\Forms\Admin\Data;


use App\Model\Database\Repository\Admin\Entity\Account;

/**
 * Class AdminFormData
 * @package App\Forms\Admin\Data
 */
class AdminFormData extends Account
{
    const permissions_array = "permissions_array";

    public array $permissions_array;
}