<?php


namespace App\Forms\Dynamic\Data;


use App\Model\Database\EAV\Translations\TranslatedValue;
use App\Model\Database\Repository\Dynamic\Entity\DynamicAttribute;

class AttributeData extends DynamicAttribute
{
    const DATA_TYPES = [
        "integer" => "Celé číslo",
        "float" => "Číslo s desetinným místem",
        "string" => "Textový řetězec",
        TranslatedValue::class => "Textový řetězec s překladem",
        "bool" => "ANO/NE"
    ];

    const GENERATED_VALUES = [
        "created" => "Datum vytvořeřní",
        "edited" => "Datum poslední změny",
        "created_admin" => "Vytvořil/a",
        "edited_admin" => "Naposledy upravil/a"
    ];
}