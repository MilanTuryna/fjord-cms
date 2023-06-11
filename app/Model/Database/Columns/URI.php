<?php


namespace App\Model\Database\Columns;

/**
 * Class URI
 * @package App\Model\Database\Common\Columns
 * Column URI representing column in URI format (ex. Jak se máš? => jak-se-mas)
 */
class URI
{
    private string $text;

    /**
     * URI constructor.
     * @param string $text
     */
    public function __construct(string $text) {
        $this->text = $text;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        // not full URI validation, but for Czech it's ok
        return strtr(strtolower($this->text), [
            " " => "-",
            "á" => "a",
            "é" => "e",
            "í" => "i",
            "ó" => "o",
            "ú" => "u",
            "ý" => "y",
            "č" => "c",
            "ď" => "d",
            "ě" => "e",
            "ň" => "n",
            "ř" => "r",
            "š" => "s",
            "ť" => "t",
            "ž" => "z",
            "ů" => "u",
            "?" => '',
            "/" => '--'
        ]);
    }
}