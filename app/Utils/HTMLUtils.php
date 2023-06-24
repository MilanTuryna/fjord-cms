<?php


namespace App\Utils;


use Nette\Utils\Html;

class HTMLUtils
{
    /**
     * @param array $data
     * @param bool $associative
     * @return Html
     */
    public static function createUlList(array $data, bool $associative = true): Html
    {
        $liArr = [];
        if($associative) {
            foreach ($data as $k => $v) {
                $iArr[] = "<li>$k: <b>$v</b></li>";
            }
        } else {
            foreach ($data as $k) {
                $iArr[] = "<li>$k</li>";
            }
        }
        return Html::fromHtml("<ul>" . implode('', $liArr) . " </ul>");
    }
}