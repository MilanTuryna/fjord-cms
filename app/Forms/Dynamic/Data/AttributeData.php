<?php


namespace App\Forms\Dynamic\Data;


use App\Forms\Dynamic\Enum\InputType;
use App\Model\Database\EAV\DataType;
use App\Model\Database\EAV\Translations\TranslatedValue;
use App\Model\Database\Repository\Dynamic\Entity\DynamicAttribute;

/**
 * Class AttributeData
 * @package App\Forms\Dynamic\Data
 */
class AttributeData extends DynamicAttribute
{
    const DATA_TYPES = [
        DataType::ARBITRARY => "Libovolný datový typ",
        DataType::INTEGER  => "Celé číslo",
        DataType::FLOAT  => "Číslo s desetinným místem",
        DataType::STRING => "Textový řetězec",
        DataType::TRANSLATED_VALUE => "Textový řetězec s překladem",
        DataType::BOOL => "ANO/NE"
    ];

    const INPUT_TYPES = [
        InputType::CLASSIC_INPUT => "Bežný input (vstup)",
        InputType::TEXTAREA => "Input jako textarea"
    ];

    const GENERATED_VALUES = [
        GeneratedValues::CREATED => "Datum vytvoření",
        GeneratedValues::EDITED => "Datum poslední změny",
        GeneratedValues::CREATED_ADMIN => "Vytvořil/a",
        GeneratedValues::EDITED_ADMIN => "Naposledy upravil/a"
    ];
}