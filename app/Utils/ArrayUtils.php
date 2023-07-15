<?php


namespace App\Utils;


use App\Model\Database\Repository\Gallery\Entity\Gallery;
use Nette\Database\Table\ActiveRow;

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

    /**
     * used in template
     * @param array $array
     * @param mixed $key
     * @return array
     */
    public static function descOrder(array $array, mixed $key): array {
        usort($array, function ($f, $s) use ($key) {
            if(!($f instanceof ActiveRow && $s instanceof ActiveRow)) {
                if(array_key_exists($key,$s) && !array_key_exists($key, $f)) $f[$key] = 0;
                if(array_key_exists($key, $f) && !array_key_exists($key, $s)) $s[$key] = 0;
            }

            return $s[$key] <=> $f[$key];
        });
        return $array;
    }

    /**
     * used in template
     * @param array $array
     * @param mixed $key
     * @return array
     */
    public static function ascOrder(array $array, mixed $key): array {
        usort($array, function ($f, $s) use ($key) {
            return $f[$key] <=> $s[$key];
        });
        bdump($array);
        return $array;
    }
}