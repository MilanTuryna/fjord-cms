<?php


namespace App\Model\UI\FlashMessages;


use App\Forms\FlashMessages;
use App\Model\UI\SpecialFlashMessage;

class AddedGalleryItems extends SpecialFlashMessage
{
    public function __construct(public array $addedItems, public string $color = FlashMessages::SUCCESS) {
    }
}