<?php

namespace App\Model\Database\EAV;

use App\Model\Database\EAV\Translations\TranslatedValue;

interface DataType
{
    const INTEGER = "integer";
    const FLOAT = "float";
    const STRING = "string";
    const TRANSLATED_VALUE = TranslatedValue::class;
    const BOOL = "bool";
}