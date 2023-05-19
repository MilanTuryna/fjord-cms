<?php


namespace App\Model\Database\Repository\Settings\Entity;


use App\Model\Database\Entity;

class GlobalSettings extends Entity
{
    const app_name = "app_name";
    const app_author = "app_author";
    const app_keywords = "app_keywords";
    const created = "created";
    const admin_id = "admin_id";

    public string $app_name;
    public string $app_author;
    public string $app_keywords;
    public string $created;
    public int $admin_id;
}