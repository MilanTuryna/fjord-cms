<?php


namespace App\Presenters;


use App\Model\Database\EAV\DynamicEntityFactory;
use App\Model\Database\Repository\Dynamic\Entity\DynamicEntity;
use App\Model\Database\Repository\Gallery\GalleryRepository;
use App\Model\Database\Repository\Settings\GlobalSettingsRepository;
use App\Model\Database\Repository\Template\Entity\Page;
use App\Model\Database\Repository\Template\Entity\Template;
use App\Model\Database\Repository\Template\PageRepository;
use App\Model\Database\Repository\Template\TemplateRepository;
use App\Model\FileSystem\Templating\TemplateUploadDataProvider;
use App\Model\FileSystem\Templating\TemplateUploadManager;
use App\Model\Templating\DataHint\FjordTemplateProviderData;
use Nette\Application\BadRequestException;
use Nette\Application\Helpers;
use Nette\Application\UI\Presenter;
use Nette\Database\Table\ActiveRow;
use Nette\Routing\Route;

/**
 * Class FrontBasePresenter
 * @package App\Presenters
 */
class FrontBasePresenter extends Presenter
{
    private ActiveRow|Template|null $usedTemplate;

    /**
     * FrontBasePresenter constructor.
     * @param TemplateRepository $templateRepository
     * @param PageRepository $pageRepository
     * @param TemplateUploadDataProvider $templateUploadDataProvider
     * @param GalleryRepository $galleryRepository
     */
    public function __construct(private TemplateRepository $templateRepository, private PageRepository $pageRepository, private TemplateUploadDataProvider $templateUploadDataProvider, private GalleryRepository $galleryRepository, private GlobalSettingsRepository $globalSettingsRepository, private DynamicEntityFactory $dynamicEntityFactory)
    {
        parent::__construct();

        $this->usedTemplate = $this->templateRepository->findByColumn(Template::used, 1)->fetch();
    }

    /**
     * @return array
     */
    public function formatTemplateFiles(): array
    {
        [$module, $presenter] = Helpers::splitName($this->getName());

        // ex. Admin/templates/Application/Settings/file.latte
        $module = explode(":", $module);
        unset($module[0]);
        if(!empty($module)) $module = implode("/", $module);

        $dir = dirname(static::getReflection()->getFileName());
        $dir = is_dir("$dir/templates") ? $dir : dirname($dir);
        return !empty($module) ? [
            "$dir/templates/$module/$presenter/$this->view.latte",
            "$dir/templates/$module/$presenter.$this->view.latte",
        ] : [
            "$dir/templates/$presenter/$this->view.latte"
        ];
    }

    public function startup()
    {
        if(!$this->usedTemplate) {
            die("Na tomto webu právě probíhá údržba. Vyčkejte než skončí, případně kontaktujte administrátora webu.");
        }
    }

    /**
     * @param string $path with slashes
     * @throws BadRequestException
     */
    public function renderUrl(string $path) {
        /**
         * @var $page Page
         */
        $pages = $this->pageRepository->findAll()->where(sprintf("%s = ? = ?", Page::template_id), $this->usedTemplate->id)->fetchAll();
        foreach ($pages as $page) {
            $request = $this->getHttpRequest();
            $route = new Route($page->route);
            $params = $route->match($request);
            if($params) {
                $templateUploadManager = new TemplateUploadManager($this->templateUploadDataProvider, $this->usedTemplate->id, TemplateUploadManager::MODE_SOLID);
                $templateFolder = $templateUploadManager->getFolderPath();
                if($page->output_type === "PATH") {
                    $fileName = $templateUploadManager->getPagesFolder() . DIRECTORY_SEPARATOR . $page->output_content;
                    if(file_exists(!$fileName)) {
                        $this->error("Soubor $fileName neexistuje");
                    }
                } else {
                    $generateFile = $templateFolder . DIRECTORY_SEPARATOR . "___gen-" . $page->id . ".latte";
                    if(file_exists($generateFile)) unlink($generateFile);
                    $newFile = fopen($generateFile, "w");
                    fwrite($newFile, $page->output_content);
                    $fileName = $generateFile;
                }
                $this->template->setParameters([
                    FjordTemplateProviderData::DYNAMIC_ENTITY_FACTORY => $this->dynamicEntityFactory,
                    FjordTemplateProviderData::SETTINGS => $this->globalSettingsRepository->getActualSettings(),
                    FjordTemplateProviderData::PARAMETERS => $params,
                ]);
                $this->template->setFile($fileName);

                break;
            } else {
                $this->error("Tato stránka nebyla nalezena. V případě, že si myslíte, že se jedná o chybu, kontaktujte administrátora", 404);
            }
        }
    }
}