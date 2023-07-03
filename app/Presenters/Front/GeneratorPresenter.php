<?php


namespace App\Presenters\Front;


use App\Model\Database\EAV\DynamicEntityFactory;
use App\Model\Database\Repository\Gallery\GalleryRepository;
use App\Model\Database\Repository\Settings\GlobalSettingsRepository;
use App\Model\Database\Repository\Template\Entity\Page;
use App\Model\Database\Repository\Template\Entity\PageVariable;
use App\Model\Database\Repository\Template\Entity\Template;
use App\Model\Database\Repository\Template\PageRepository;
use App\Model\Database\Repository\Template\PageVariableRepository;
use App\Model\Database\Repository\Template\TemplateRepository;
use App\Model\FileSystem\Gallery\GalleryFacadeFactory;
use App\Model\FileSystem\Templating\TemplateUploadDataProvider;
use App\Model\FileSystem\Templating\TemplateUploadManager;
use App\Model\Templating\DataHint\FjordTemplateProviderData;
use App\Presenters\FrontBasePresenter;
use App\Utils\FormatUtils;
use Nette\Application\AbortException;
use Nette\Application\BadRequestException;
use Nette\Application\Responses\FileResponse;
use Nette\Routing\Route;
use Nette\Utils\Finder;

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
     * @throws BadRequestException
     */
    public function render404(): void {
        $templateUploadManager = new TemplateUploadManager($this->templateUploadDataProvider, $this->usedTemplate->dirname, TemplateUploadManager::MODE_SOLID);
        $error404file = $templateUploadManager->getTemplateFolder($this->usedTemplate->zip_name) . DIRECTORY_SEPARATOR . $this->usedTemplate->error404;
        if(!file_exists($error404file)) {
            $this->error("Tato stránka nebyla nalezena. V případě, že si myslíte, že se jedná o chybu, kontaktujte administrátora", 404);
        }
        $this->template->setFile($error404file);
        $this->getHttpResponse()->setCode(404);
    }

    /**
     * @throws AbortException|BadRequestException
     */
    public function renderDependencies(string $path): void {
        // some security for hacks
        if($path = "/") $this->redirect("404");
        $explodedPath = explode("/", $path);
        $fileName = $explodedPath[array_key_last($explodedPath)];
        if(str_starts_with(".",$fileName)) $this->redirect("404");
        $tempUploadManager = new TemplateUploadManager($this->templateUploadDataProvider, $this->usedTemplate->dirname, TemplateUploadManager::MODE_SOLID);
        $dependencyPath = $tempUploadManager->getDependencyFolder($this->usedTemplate->dependency_path);
        $realFilePath = realpath($path);
        foreach (Finder::findFiles($dependencyPath . DIRECTORY_SEPARATOR . "*") as $file) {
            if($file === $realFilePath) {
                $fileResponse = new FileResponse($file, $fileName, FormatUtils::get_mime_type($fileName), false);
                $this->sendResponse($fileResponse);
            }
        };
        $this->render404();
    }

    /**
     * @param string $path with slashes
     * @throws BadRequestException
     */
    public function renderUrl(string $path = "/"): void
    {
        /**
         * @var $page Page
         */
        $pages = $this->pageRepository->findByColumn(Page::template_id, $this->usedTemplate->id)->fetchAll();
        $matches = 0;
        foreach ($pages as $page) {
            $request = $this->getHttpRequest();
            $route = new Route($page->route);
            $params = $route->match($request);
            if (is_array($params)) {
                $matches++;
                $templateUploadManager = new TemplateUploadManager($this->templateUploadDataProvider, $this->usedTemplate->dirname, TemplateUploadManager::MODE_SOLID);
                $templateFolder = $templateUploadManager->getTemplateFolder($this->usedTemplate->zip_name);
                if ($page->output_type === "PATH") {
                    $fileName = $templateFolder . $page->output_content;
                    if (!file_exists($fileName)) {
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
                $providerData->dependencyPath = $templateUploadManager->getDependencyFolder($this->usedTemplate->dependency_path);
                $providerData->dynamicEntityFactory = $this->dynamicEntityFactory;
                $providerData->galleryFacadeFactory = $this->galleryFacadeFactory;
                $providerData->settings = $this->globalSettingsRepository->getActualSettings();
                $providerData->parameters = $params;
                $providerData->templateInfo = $this->usedTemplate;
                $providerData->variables = $varsAssociativeArray;

                $this->template->setParameters(["fjord" => $providerData]);
                $this->template->setFile($fileName);
                break;
            }
        }
        if(!$matches) {
            $templateUploadManager = new TemplateUploadManager($this->templateUploadDataProvider, $this->usedTemplate->dirname, TemplateUploadManager::MODE_SOLID);
            $error404file = $templateUploadManager->getTemplateFolder($this->usedTemplate->zip_name) . DIRECTORY_SEPARATOR . $this->usedTemplate->error404;
            if(!file_exists($error404file)) {
                $this->error("Tato stránka nebyla nalezena. V případě, že si myslíte, že se jedná o chybu, kontaktujte administrátora", 404);
            }
            $this->template->setFile($error404file);
        }
    }
}