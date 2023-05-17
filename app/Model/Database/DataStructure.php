<?php

namespace App\Model\Database;

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