<?php


namespace App\Presenters;

/**
 * Class TemplateUtils
 * @package App\Presenters
 */
class TemplateUtils
{
    /**
     * @param string $fullRoute
     * @return array
     * Dont deleted, used in template
     */
    public static function stringRedirect(string $fullRoute): array {
        $split = explode(", ", $fullRoute); // [0] = route, [1] = args
        return [$split[0], array_key_exists(1, $split) ? $split[1] : null];
    }

    /**
     * @param string $text
     * @return array
     */
    public static function toDeclension(string $text): array
    {
        return explode(",", $text);
    }

    public static function fromDeclension(array $declension): string
    {
        return implode(",", $declension);
    }
}