<?php


namespace App\Forms;

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
    public array $args;

    /**
     * FormRedirect constructor.
     * @param string $route
     * @param array $args
     */
    public function __construct(string $route, array $args = []) {
        $this->route = $route;
        $this->args = $args;
    }
}