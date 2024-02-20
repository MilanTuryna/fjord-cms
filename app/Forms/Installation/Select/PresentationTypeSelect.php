<?php

namespace App\Forms\Installation\Select;

final class PresentationTypeSelect
{
    const WITH_TEMPLATE = "with_template";
    const WITHOUT_TEMPLATE = "without_template";

    public static function selectBox(): array
    {
        return [
            self::WITH_TEMPLATE => "Web se šablonou",
            self::WITHOUT_TEMPLATE => "Systém bez šablony a webové prezentace"
        ];
    }
}