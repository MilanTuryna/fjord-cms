<?php


namespace App\Model\Templating\DataHint;

use App\Model\Database\EAV\DynamicEntityFactory;
use App\Model\Database\Repository\Product\ProductRepository;
use App\Model\Database\Repository\Settings\Entity\GlobalSettings;
use App\Model\Database\Repository\SMTP\Entity\Server;
use App\Model\Database\Repository\Template\Entity\Template;
use App\Model\FileSystem\Gallery\GalleryFacadeFactory;
use Nette\Database\Table\ActiveRow;
use Nette\Utils\ArrayHash;

// TODO: better optimalization and use collections instead for DB
class FjordTemplateProviderData
{
    const GALLERIES = "galleries";
    const DYNAMIC_ENTITY_FACTORY = "dynamicEntityFactory";
    const SETTINGS = "settings" ;
    const PARAMETERS = "parameters";
    const VARIABLES = "variables";

    public string $dependencyPath; // path for useful files like js scripts, css etc.
    public string $path404;
    public ProductRepository $productRepository;
    public Server|ActiveRow|null $activeSmtpServer;
    public GalleryFacadeFactory $galleryFacadeFactory;
    public DynamicEntityFactory $dynamicEntityFactory;
    public GlobalSettings|ActiveRow|null $settings;
    public array $parameters; // parsed query parameters from URL in associative array (probably, untested, it's from Nette Router -> match())
    public Template|ActiveRow|null $templateInfo;
    public array|ArrayHash $variables; // variable settings for selected page, in associative array [PageVariable::id_name => PageVariable::content]
}