<?php


namespace App\Model\Database\EAV\Translations;


use App\Model\Database\EAV\Translations\Exception\LanguageNotFoundException;
use Nette\Utils\Json;
use Nette\Utils\JsonException;

/**
 * Class TranslatedValue
 * @package App\Model\Database\EAV\Translations
 */
class TranslatedValue
{
    /**
     * @var array|mixed|string[]
     */
    public array $languages = [];

    /**
     */
    public function __construct(array $languages) {
        $this->languages = $languages;
    }

    /**
     * @throws JsonException
     */
    public function importJson(string $json) {
        $this->languages = Json::decode($json, true);
    }

    /**
     * @throws LanguageNotFoundException
     */
    public function getLanguage(string $language): string {
        if(!isset($this->languages[$language])) {
            throw new LanguageNotFoundException("Language '" . $language . "' not found!");
        }
        return $this->languages[$language];
    }

    /**
     * @throws JsonException
     */
    public function __toString(): string {
        return Json::encode($this->languages);
    }
}