<?php


namespace App\Model\FileSystem\Templating;

/**
 * Class TemplateUploadDataProvider
 * @package App\Model\FileSystem\Templating
 */
class TemplateUploadDataProvider
{
    public function __construct(public string $tempStorage, public string $solidStorage) {
    }
}