<?php

namespace App\Model\Database\EAV;

use App\Model\Database\EAV\Translations\TranslatedValue;
use Nette\Utils\DateTime;

/**
 * Interface DataType
 * @package App\Model\Database\EAV
 */
interface DataType
{
    const INTEGER = "integer";
    const FLOAT = "float";
    const STRING = "string";
    const TRANSLATED_VALUE = TranslatedValue::class;
    const DATE_TIME = DateTime::class;
    const ARBITRARY = "ARBITRARY";
    const BOOL = "bool";
}