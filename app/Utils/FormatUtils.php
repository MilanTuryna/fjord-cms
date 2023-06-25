<?php


namespace App\Utils;


class FormatUtils
{
    /**
     * @param $size
     * @return string
     */
    public static function formatBytes($size): string {
        if((int)$size === 0) return "0 B";
        $base = log($size) / log(1024);
        $suffix = array("B", "KB", "MB", "GB", "TB");
        $f_base = floor($base);
        return round(pow(1024, $base - floor($base)), 1) . $suffix[$f_base];
    }

    /**
     * @param string $inputName
     * @return bool
     */
    public static function validateInputName(string $inputName): bool {
        return preg_match("#^[a-zA-Z0-9_]+$#D", $inputName);
    }
}