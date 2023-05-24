<?php


namespace App\Forms;


class FormMessage
{
    const DEFAULT_SUCCESS_MESSAGE = "Záznam byl úspěšně aktualizován.";
    const DEFAULT_ERROR_MESSAGE = "Záznam nemohl být z neznámého důvodu aktualizován.";

    public string $success;
    public string $error;
    /**
     * [Exception:class => 'message']
     * @var array $catchMessages
     */
    public array $catchMessages;

    /**
     * FormMessage constructor.
     * @param string $success
     * @param string $error
     * @param array $catchMessages
     */
    public function __construct(string $success = self::DEFAULT_SUCCESS_MESSAGE, string $error = self::DEFAULT_ERROR_MESSAGE, array $catchMessages = []) {
        $this->success = $success;
        $this->error = $error;
        $this->catchMessages = $catchMessages;
    }

    /**
     * @param string $exceptionName
     * @param string $message
     */
    public function addCatchMessage(string $exceptionName, string $message): void {
        $this->catchMessages[$exceptionName] = $message;
    }

    /**
     * @param string $exceptionName
     */
    public function removeMessage(string $exceptionName): void {
        unset($this->catchMessages[$exceptionName]);
    }
}