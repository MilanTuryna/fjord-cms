<?php


namespace App\Model\UI\FlashMessages;


use App\Model\UI\SpecialFlashMessage;

/**
 * Class GeneratedJsonSchema
 * @package App\Model\UI\FlashMessages
 */
class GeneratedJsonSchema extends SpecialFlashMessage
{
    public function __construct(public string $convertedText)
    {
    }
}