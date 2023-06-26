<?php


namespace App\Forms;

use JetBrains\PhpStorm\NoReturn;
use JetBrains\PhpStorm\Pure;
use Nette\Application\AbortException;
use Nette\Application\UI\Presenter;

/**
 * Class FormRedirect
 * @package App\Forms
 */
class FormRedirect
{
    /**
     * Special constant (can be used in $args and will be changed into last inserted id)
     */
    const LAST_INSERT_ID = "___LAST_INSERT_ID___";

    public string $route;
    public array $args = [];

    /**
     * FormRedirect constructor.
     * @param string $route
     * @param array|string $args
     */
    public function __construct(string $route, array|string $args = []) {
        $this->route = $route;
        $this->args = is_string($args) ? [$args] : $args;
    }

    /**
     * @param Presenter $presenter
     * @param int|null $lastInsertedId
     * @throws AbortException
     */
    #[NoReturn] public function presenter(Presenter $presenter, ?int $lastInsertedId = null): void {
        $presenter->redirect($this->route, array_map(function ($arg) use($lastInsertedId) {
            return $arg === FormRedirect::LAST_INSERT_ID ? $lastInsertedId : $arg;
        }, $this->args));
    }

    #[Pure] public static function this(): self {
        return new self("this");
    }
}