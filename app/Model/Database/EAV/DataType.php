<?php

namespace App\Model\Database\EAV;

use App\Model\Database\EAV\Translations\TranslatedValue;
use Nette\Utils\DateTime;

/**
 * Interface DataType
 * @package App\Model\Database\EAV
 */
class DataType
{
    const INTEGER = "integer";
    const FLOAT = "float";
    const STRING = "string";
    const TRANSLATED_VALUE = TranslatedValue::class;
    const DATE_TIME = DateTime::class;
    const ARBITRARY = "ARBITRARY";
    const BOOL = "bool";

    /**
     * @return string[]
     */
    public static function arr(): array {
        return [
            DataType::INTEGER, DataType::FLOAT, DataType::STRING, DataType::TRANSLATED_VALUE, DataType::DATE_TIME, DataType::ARBITRARY, DataType::BOOL
        ];
    }
}