<?php


namespace App\Model\Http\WebLoader\Specific;


use App\Model\Database\Repository\Template\Entity\Template;
use App\Model\FileSystem\Templating\TemplateUploadDataProvider;
use App\Model\FileSystem\Templating\TemplateUploadManager;
use App\Model\Http\WebLoader\FileMask;
use App\Model\Http\WebLoader\Module;
use JetBrains\PhpStorm\Pure;
use Nette\Database\Table\ActiveRow;

/**
 * Class DynamicDependencyModule
 * @package App\Model\Http\WebLoader\Specific
 */
class DynamicDependencyModule extends Module
{
    /**
     * DynamicDependencyModule constructor.
     * @param Template|ActiveRow $templateRow
     * @param TemplateUploadDataProvider $templateUploadDataProvider
     */
    public function __construct(Template|ActiveRow $templateRow, TemplateUploadDataProvider $templateUploadDataProvider)
    {
        $uploadManager = new TemplateUploadManager($templateUploadDataProvider, $templateRow->dirname, TemplateUploadManager::MODE_SOLID);
        $dependencyFolder = $uploadManager->getDependencyFolder($templateRow->zip_name, $templateRow->dependency_path);
        $cssMask = new FileMask($dependencyFolder, ["*.css"]);
        $jsMask = new FileMask($dependencyFolder, ["*.js"]);
        parent::__construct($cssMask, $jsMask, "DynamicDependencyModule");
    }
}