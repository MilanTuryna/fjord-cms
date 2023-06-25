<?php


namespace App\Model\FileSystem\Templating;


use App\Model\Cryptography;
use App\Model\FileSystem\UploadManager;
use Exception;
use JetBrains\PhpStorm\Pure;

class TemplateUploadManager extends UploadManager
{
    const ALLOWED_EXTENSIONS = ["zip"];

    const MODE_SOLID = "mode_solid";
    const MODE_TEMP = "mode_temp";

    /**
     * TemplateUploadManager constructor.
     * @param TemplateUploadDataProvider $templateUploadDataProvider
     * @param string $templateFolderName
     * @param string $mode
     */
    #[Pure] public function __construct(private TemplateUploadDataProvider $templateUploadDataProvider, private string $templateFolderName, private string $mode)
    {
        parent::__construct(($this->mode === self::MODE_SOLID ? $this->templateUploadDataProvider->solidStorage
            : $this->templateUploadDataProvider->tempStorage ) . DIRECTORY_SEPARATOR . $this->templateFolderName,
            self::ALLOWED_EXTENSIONS);

    }

    /**
     * @throws Exception
     */
    public static function createUniqueName(string $originalTemplateName): string
    {
        return $originalTemplateName . Cryptography::createUnique(5);
    }

    // returns path to folder with templateFolderName
    public function getFolderPath(): string {
        return $this->path;
    }
}