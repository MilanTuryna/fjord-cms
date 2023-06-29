<?php


namespace App\Model\Database\Repository\Settings\Entity;


use App\Model\Database\Entity;

/**
 * Class GlobalSettings
 * @package App\Model\Database\Repository\Settings\Entity
 */
class GlobalSettings extends Entity
{
    const app_name = "app_name";
    const app_author = "app_author";
    const app_keywords = "app_keywords";
    const default_language = "default_language";
    const languages = "languages";
    const created = "created";
    const admin_id = "admin_id";

    public string $app_name;
    public string $app_author;
    public string $app_keywords;
    public string $created;
    public string $languages;
    public string $default_language;
    public int $admin_id;
}