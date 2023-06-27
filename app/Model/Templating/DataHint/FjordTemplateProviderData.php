<?php


namespace App\Model\Templating\DataHint;

use App\Model\Database\EAV\DynamicEntityFactory;
use App\Model\Database\Repository\Settings\Entity\GlobalSettings;
use App\Model\Database\Repository\Template\Entity\Template;
use App\Model\FileSystem\Gallery\GalleryFacadeFactory;
use Nette\Database\Table\ActiveRow;

// TODO: better optimalization and use collections instead for DB
class FjordTemplateProviderData
{
    const GALLERIES = "galleries";
    const DYNAMIC_ENTITY_FACTORY = "dynamicEntityFactory";
    const SETTINGS = "settings" ;
    const PARAMETERS = "parameters";

    public GalleryFacadeFactory $galleryFacadeFactory;
    public DynamicEntityFactory $dynamicEntityFactory;
    public GlobalSettings|ActiveRow|null $settings;
    public array $parameters; // associative array
    public Template|ActiveRow|null $templateInfo;
}