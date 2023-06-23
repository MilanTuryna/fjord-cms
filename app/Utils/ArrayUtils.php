<?php


namespace App\Utils;


class ArrayUtils
{
    /**
     * @param array $list
     * @param string|object $class
     * @return array
     */
    public static function getElementsWithType(array $list, string|object $class): array {
        $result = [];
        foreach ($list as $x) {
            if($x instanceof $result) $result[] = $x;
        }
        return $result;
    }
}