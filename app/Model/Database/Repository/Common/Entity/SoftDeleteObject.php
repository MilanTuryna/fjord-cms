<?php


namespace App\Model\Database\Repository\Common\Entity;


use App\Model\Database\Entity;

/**
 * Class SoftDeleteObject
 * @package App\Model\Database\Repository\Common\Entity
 */
class SoftDeleteObject extends Entity
{
    const deleted = "deleted";

    public bool $deleted;
}