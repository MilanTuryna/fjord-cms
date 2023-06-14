<?php


namespace App\Model\Database\Repository\Admin\Entity;


use App\Model\Database\Entity;

/**
 * Class AccessLogPresenter
 * @package App\Model\Database\Repository\Admin\Entity
 */
class AccessLog extends Entity
{
    const admin_id = "admin_id";
    const ip = "ip";
    const device = "device";
    const created = "created";

    public string $ip;
    public string $device;
    public string $created;
    public string $admin_id;
}