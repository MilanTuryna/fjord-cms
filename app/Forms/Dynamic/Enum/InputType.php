<?php


namespace App\Forms\Dynamic\Enum;

/**
 * Class InputType
 * @package App\Forms\Dynamic\Enum
 */
class InputType
{
    const CLASSIC_INPUT = "classic_input";
    const TEXTAREA = "textarea";
    const DATE_INPUT = "date_input";
    const COLOR_INPUT = "color_input";

    /**
     * @return string[]
     */
    public static function arr(): array {
        return [InputType::CLASSIC_INPUT, InputType::TEXTAREA, InputType::DATE_INPUT, InputType::COLOR_INPUT];
    }
}