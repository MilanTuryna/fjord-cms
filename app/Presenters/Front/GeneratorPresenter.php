<?php


namespace App\Presenters\Front;


use App\Model\Database\EAV\DynamicEntityFactory;
use App\Model\Database\Repository\Gallery\GalleryRepository;
use App\Model\Database\Repository\Settings\GlobalSettingsRepository;
use App\Model\Database\Repository\Template\Entity\Page;
use App\Model\Database\Repository\Template\Entity\PageVariable;
use App\Model\Database\Repository\Template\PageRepository;
use App\Model\Database\Repository\Template\PageVariableRepository;
use App\Model\Database\Repository\Template\TemplateRepository;
use App\Model\FileSystem\Gallery\GalleryFacadeFactory;
use App\Model\FileSystem\Templating\TemplateUploadDataProvider;
use App\Model\FileSystem\Templating\TemplateUploadManager;
use App\Model\Templating\DataHint\FjordTemplateProviderData;
use App\Presenters\FrontBasePresenter;
use Nette\Application\BadRequestException;
use Nette\Routing\Route;

/**
 * Class GeneratorPresenter
 * @package App\Presenters\Front
 */
class GeneratorPresenter extends FrontBasePresenter
{
    /**
     * GeneratorPresenter constructor.
     * @param TemplateRepository $templateRepository
     * @param PageRepository $pageRepository
     * @param TemplateUploadDataProvider $templateUploadDataProvider
     * @param GalleryRepository $galleryRepository
     * @param GlobalSettingsRepository $globalSettingsRepository
     * @param DynamicEntityFactory $dynamicEntityFactory
     * @param GalleryFacadeFactory $galleryFacadeFactory
     * @param PageVariableRepository $pageVariableRepository
     */
    public function __construct(private TemplateRepository $templateRepository, private PageRepository $pageRepository, private TemplateUploadDataProvider $templateUploadDataProvider,
                                private GalleryRepository $galleryRepository,
                                private GlobalSettingsRepository $globalSettingsRepository, private DynamicEntityFactory $dynamicEntityFactory,
                                private GalleryFacadeFactory $galleryFacadeFactory, private PageVariableRepository $pageVariableRepository)
    {
        parent::__construct($this->templateRepository);
    }

    /**
     * @param string $path with slashes
     * @throws BadRequestException
     */
    public function renderUrl(string $path)
    {
        /**
         * @var $page Page
         */
        $pages = $this->pageRepository->findAll()->where(sprintf("%s = ? = ?", Page::template_id), $this->usedTemplate->id)->fetchAll();
        foreach ($pages as $page) {
            $request = $this->getHttpRequest();
            $route = new Route($page->route);
            $params = $route->match($request);
            if ($params) {
                $templateUploadManager = new TemplateUploadManager($this->templateUploadDataProvider, $this->usedTemplate->id, TemplateUploadManager::MODE_SOLID);
                $templateFolder = $templateUploadManager->getFolderPath();
                if ($page->output_type === "PATH") {
                    $fileName = $templateUploadManager->getPagesFolder() . DIRECTORY_SEPARATOR . $page->output_content;
                    if (file_exists(!$fileName)) {
                        $this->error("Soubor $fileName neexistuje");
                    }
                } else {
                    $generateFile = $templateFolder . DIRECTORY_SEPARATOR . "___gen-" . $page->id . ".latte";
                    if (file_exists($generateFile)) unlink($generateFile);
                    $newFile = fopen($generateFile, "w");
                    fwrite($newFile, $page->output_content);
                    $fileName = $generateFile;
                }

                /**
                 * @var $templateVariables PageVariable[]
                 */
                $templateVariables = $this->pageVariableRepository->findByColumn(PageVariable::page_id, $page->id);
                $varsAssociativeArray = [];
                foreach ($templateVariables as $templateVariable) {
                    $varsAssociativeArray[$templateVariable->id_name] = $templateVariable->content;
                }

                $providerData = new FjordTemplateProviderData();
                $providerData->dynamicEntityFactory = $this->dynamicEntityFactory;
                $providerData->galleryFacadeFactory = $this->galleryFacadeFactory;
                $providerData->settings = $this->globalSettingsRepository->getActualSettings();
                $providerData->parameters = $params;
                $providerData->templateInfo = $this->usedTemplate;
                $providerData->variables = $varsAssociativeArray;

                $this->template->setParameters(["fjord" => $providerData]);
                $this->template->setFile($fileName);
                break;
            } else {
                $this->error("Tato stránka nebyla nalezena. V případě, že si myslíte, že se jedná o chybu, kontaktujte administrátora", 404);
            }
        }
    }
}