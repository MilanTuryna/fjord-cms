<?php


namespace App\Model\FileSystem\Templating;


use App\Model\Cryptography;
use App\Model\FileSystem\UploadManager;
use Exception;
use JetBrains\PhpStorm\Pure;

/**
 * Class TemplateUploadManager
 * @package App\Model\FileSystem\Templating
 */
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

    // returns folder with zip and extracted dir with zip_name (without .zip)
    public function getLocalFolder(): string {
        return $this->path;
    }

    /**
     * @param string $zipName
     * @param string $dependencyLocalPath
     * @return string
     */
    public function getDependencyFolder(string $zipName, string $dependencyLocalPath): string {
        return $this->getTemplateFolder($zipName) . DIRECTORY_SEPARATOR . $dependencyLocalPath;
    }

    // returns path to folder with templateFolderName
    public function getTemplateFolder(string $zipName): string {
        $exploded = explode(".", $zipName);
        array_pop($exploded);
        $folderName = implode($exploded);
        return $this->path . DIRECTORY_SEPARATOR . $folderName;
    }

    /**
     * @param string $zipName
     * @return string
     */
    public function getPagesFolder(string $zipName): string {
        $exploded = explode(".", $zipName);
        array_pop($exploded);
        $folderName = implode($exploded);
        return $this->path . DIRECTORY_SEPARATOR . $folderName . DIRECTORY_SEPARATOR . "pages";
    }
}