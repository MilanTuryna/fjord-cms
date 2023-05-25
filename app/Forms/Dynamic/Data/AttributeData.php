<?php


namespace App\Forms\Dynamic\Data;


use App\Model\Database\EAV\DataType;
use App\Model\Database\EAV\Translations\TranslatedValue;
use App\Model\Database\Repository\Dynamic\Entity\DynamicAttribute;

class AttributeData extends DynamicAttribute
{
    const DATA_TYPES = [
        DataType::INTEGER  => "Celé číslo",
        DataType::FLOAT  => "Číslo s desetinným místem",
        DataType::STRING => "Textový řetězec",
        DataType::TRANSLATED_VALUE => "Textový řetězec s překladem",
        DataType::BOOL => "ANO/NE"
    ];

    const GENERATED_VALUES = [
        "created" => "Datum vytvořeřní",
        "edited" => "Datum poslední změny",
        "created_admin" => "Vytvořil/a",
        "edited_admin" => "Naposledy upravil/a"
    ];
}