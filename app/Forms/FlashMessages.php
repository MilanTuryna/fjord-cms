<?php


namespace App\Forms;


use Nette\Utils\Html;

/**
 * Class FlashMessages
 * @package App\Forms
 */
final class FlashMessages
{
    private function __construct()
    {
    }

    /**
     * @param string $before
     * @param string $bold
     * @param string $after
     * @return Html
     */
    public static function useBold(string $before, string $bold, string $after): Html
    {
        return Html::el()->addText($before)->addHtml(Html::el('b')->setText($bold))->addText($after);
    }

    const ERROR = "danger";
    const SUCCESS = "success";
    const INFO = "info";
}