<?php

namespace App\Model\Database;

use App\Model\Database\Repository\Admin\Entity\AccessLog;
use App\Model\Database\Repository\Admin\Entity\Account;
use App\Model\Database\Repository\Dynamic\Entity\DynamicAttribute;
use App\Model\Database\Repository\Dynamic\Entity\DynamicEntity;
use App\Model\Database\Repository\Dynamic\Entity\DynamicId;
use App\Model\Database\Repository\Dynamic\Entity\DynamicValue;
use App\Model\Database\Repository\Gallery\Entity\Gallery;
use App\Model\Database\Repository\Gallery\Entity\GalleryItem;
use App\Model\Database\Repository\Settings\Entity\GlobalSettings;
use Exception;
use Nette\Utils\DateTime;

/**
 * Class DataStructure
 * @package App\Model\Database
 */
abstract class DataStructure
{
    const MAXIMUM_SQL_DATE_VALUE = "9999-12-31 23:59:59";
    const MAXIMUM_LIMIT = 999999999;

    const ENTITIES = [
        "fjord_admin_accesslog" => AccessLog::class,
        "fjord_admin_account" => Account::class,
        "fjord_dynamic_attributes" => DynamicAttribute::class,
        "fjord_dynamic_entities" => DynamicEntity::class,
        "fjord_dynamic_ids" => DynamicId::class,
        "fjord_dynamic_values" => DynamicValue::class,
        "fjord_gallery" => Gallery::class,
        "fjord_gallery_items" => GalleryItem::class,
        "fjord_global_settings" => GlobalSettings::class,
    ];

    /**
     * @param string $date
     * @return string
     * @throws Exception
     */
    public static function toTimestamp(string $date): string {
        $dateTime = DateTime::from($date);
        return $dateTime->getTimestamp();
    }

    /**
     * @param string $table
     * @param array $rows
     * @return array
     */
    public static function toJSON(string $table, array $rows): array {
        return [
            $table => $rows
        ];
    }

    /**
     * @param $rows
     * @return array
     */
    public static function fetchAllToArray($rows): array {
        return array_values(array_map(function ($activeRow) {
            return $activeRow->getIterator();
        }, $rows));
    }
}