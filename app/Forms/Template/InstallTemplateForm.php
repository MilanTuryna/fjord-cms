<?php

namespace App\Forms\Template;

use App\Forms\FlashMessages;
use App\Forms\Form;
use App\Forms\FormMessage;
use App\Forms\FormOption;
use App\Forms\FormRedirect;
use App\Forms\RepositoryForm;
use App\Forms\Template\Data\TemplateFormInstallationData;
use App\Model\Cryptography;
use App\Model\Database\EAV\DynamicEntityFactory;
use App\Model\Database\IRepository;
use App\Model\Database\Repository\Dynamic\Entity\DynamicAttribute;
use App\Model\Database\Repository\Dynamic\Entity\DynamicEntity;
use App\Model\Database\Repository\Template\AuthorRepository;
use App\Model\Database\Repository\Template\Entity\Author;
use App\Model\Database\Repository\Template\Entity\Page;
use App\Model\Database\Repository\Template\Entity\PageVariable;
use App\Model\Database\Repository\Template\Entity\Template;
use App\Model\Database\Repository\Template\PageRepository;
use App\Model\Database\Repository\Template\PageVariableRepository;
use App\Model\Database\Repository\Template\TemplateRepository;
use App\Model\FileSystem\Templating\TemplateUploadDataProvider;
use App\Model\FileSystem\Templating\TemplateUploadManager;
use App\Model\Templating\Schema\IndexJsonSchema;
use App\Utils\HTMLUtils;
use Exception;
use JetBrains\PhpStorm\Pure;
use Nette\Application\UI\Presenter;
use Nette\Schema\Processor;
use Nette\Schema\ValidationException;
use Nette\Utils\DateTime;
use Nette\Utils\FileSystem;
use Nette\Utils\Html;
use Nette\Utils\Json;
use Tester\Expect;

/**
 * Class InstallTemplateForm
 * @package App\Forms\Template
 */
class InstallTemplateForm extends RepositoryForm
{
    /**
     * InstallTemplateForm constructor.
     * @param Presenter $presenter
     * @param TemplateRepository $templateRepository
     * @param TemplateUploadDataProvider $templateUploadDataProvider
     * @param DynamicEntityFactory $dynamicEntityFactory
     * @param AuthorRepository $authorRepository
     * @param PageRepository $pageRepository
     * @param PageVariableRepository $pageVariableRepository
     * @param FormRedirect $formRedirect
     */
    #[Pure] public function __construct(Presenter $presenter, private TemplateRepository $templateRepository, private TemplateUploadDataProvider $templateUploadDataProvider,
                                        private DynamicEntityFactory $dynamicEntityFactory, private AuthorRepository $authorRepository, private PageRepository $pageRepository, private PageVariableRepository $pageVariableRepository,
                                        private FormRedirect $formRedirect)
    {
        parent::__construct($presenter, $this->templateRepository);

        $this->presenter = $presenter;
    }

    /**
     * @return \Nette\Application\UI\Form
     */
    public function create(): \Nette\Application\UI\Form
    {
        $form = parent::create();
        $form->addUpload("installation_zip", "Instalační soubor")->setOption(FormOption::OPTION_NOTE, "Nahrajte prosím instalační soubor .ZIP z důvěryhodné zdroje.")->setRequired(true);
        $form->addSubmit("submit", "Nainstalovat šablonu");
        return $form;
    }

    /**
     * @throws Exception
     */
    public function success(\Nette\Application\UI\Form $form, TemplateFormInstallationData $data) {
        $zipName = $data->installation_zip->getSanitizedName();
        if($data->installation_zip->isOk() && str_ends_with($zipName, "zip")) {
            $zipArchive = new \ZipArchive();
            $compressedFile = $data->installation_zip->getTemporaryFile();
            $result = $zipArchive->open($compressedFile);
            if($result) {
                $uniqueName = TemplateUploadManager::createUniqueName(implode("", explode(".", $zipName)));
                $zipArchive->renameName($zipName, $uniqueName);
                $tempUploadManager = new TemplateUploadManager($this->templateUploadDataProvider, $uniqueName, TemplateUploadManager::MODE_TEMP);
                $temporaryFolderPath = $tempUploadManager->getLocalFolder();
                $zipArchive->extractTo($temporaryFolderPath);
                $tempUploadManager->add($data->installation_zip, $uniqueName . ".zip");
                $rawIndexJSON = null;
                foreach ($tempUploadManager->getUploads() as $file) {
                    if (str_ends_with(strtolower($file), "index.json")) {
                        $rawIndexJSON = FileSystem::read($file);
                        break;
                    }
                }
                if (!$rawIndexJSON) $form->addError("V instalačním balíčku nebyl nalezen hlavní soubor inicializace. (index.json)");
                $parsedIndexJSON = json_decode($rawIndexJSON, true);
                $processor = new Processor();
                try {
                    $validation = $processor->process(IndexJsonSchema::getSchema(), $parsedIndexJSON);
                    if ($validation) {
                        $ulListData = [];
                        $entities = $parsedIndexJSON["eav"];
                        foreach ($entities as $entity) {
                            if ($this->dynamicEntityFactory->isEntityExist($entity["entity_name"])) {
                                $form->addError("Došlo ke kolizi názvů jednotlivých EAV entit. Instalace balíčku nemůže pokračovat.");
                            }
                            $entityEntity = new DynamicEntity();
                            $entityEntity->name = $entity["entity_name"];
                            if($entity["entity_description"]) $entityEntity->description = $entity["entity_description"];
                            $entityEntity->created = new DateTime();
                            $entityEntity->edited = new DateTime();
                            $entityEntity->generated_by = $uniqueName;
                            $entityEntity->menu_item_name = $entity["entity_menu_item_name"];
                            $attributeEntities = [];
                            foreach ($entity["attributes"] as $attribute) {
                                $attributeEntity = new DynamicAttribute();
                                $attributeEntity->id_name = $attribute["id_name"];
                                $attributeEntity->title = $attribute["title"];
                                $attributeEntity->data_type = $attribute["data_type"];
                                $attributeEntity->input_type = $attribute["input_type"];
                                if (isset($attribute["description"])) $attributeEntity->description = $attribute["description"];
                                if (isset($attribute["placeholder"])) $attributeEntity->placeholder = $attribute["placeholder"];
                                if (isset($attribute["generate_value"])) $attributeEntity->placeholder = $attribute["placeholder"];
                                if (isset($attribute["preset_value"])) $attributeEntity->placeholder = $attribute["placeholder"];
                                if(isset($attribute["enabled_wysiwyg"])) $attributeEntity->enabled_wysiwyg = $attribute["enabled_wysiwyg"];
                                $attributeEntity->required = $attribute["required"];
                                $attributeEntities[] = $attributeEntity;
                            }
                            $this->dynamicEntityFactory->createEntity($entityEntity, $attributeEntities);
                            $ulListData[] = $entityEntity->name;
                        }

                        // create author entity
                        $authorEntity = new Author();
                        $authorEntity->name = $parsedIndexJSON["author"]["name"];
                        if($parsedIndexJSON["author"]["website"]) $authorEntity->website = $parsedIndexJSON["author"]["website"];
                        if($parsedIndexJSON["author"]["email"]) $authorEntity->email = $parsedIndexJSON["author"]["email"];
                        $authorId = $this->authorRepository->insert($authorEntity->iterable())->id;

                        //create template entity
                        $templateEntity = new Template();
                        $templateEntity->created = new DateTime();
                        $templateEntity->edited = new DateTime();
                        $templateEntity->author_id = $authorId;
                        $templateEntity->dirname = $uniqueName;
                        $templateEntity->zip_name = $zipName;
                        $templateEntity->dependency_path = $parsedIndexJSON["dependency_path"];
                        if(isset($parsedIndexJSON["error404"]) && $parsedIndexJSON["error404"]) $templateEntity->error404 = $parsedIndexJSON["error404"];
                        $templateEntity->title = $parsedIndexJSON["title"];
                        if (isset($parsedIndexJSON["description"])) $templateEntity->description = $parsedIndexJSON["description"];
                        $templateEntity->version = $parsedIndexJSON["version"];
                        $templateEntity->used = 0;
                        $solidUploadManager = new TemplateUploadManager($this->templateUploadDataProvider, $uniqueName, TemplateUploadManager::MODE_SOLID);
                        FileSystem::rename($temporaryFolderPath, $solidUploadManager->getLocalFolder());
                        $tempUploadManager->deleteUploads(); // delete temporary files

                        $this->successTemplate($form, $templateEntity->iterable(), new FormMessage("Daná šablona byla úspěšně nainstalovaná, nyní jí můžete nastavit.",
                            "Daná šablona nemohla být z neznámého důvodu nainstalovaná. Nastala chyba v databázi."),
                            $this->formRedirect, null, [], false, true, function ($id) use ($ulListData, $parsedIndexJSON) {
                                $this->presenter->flashMessage(Html::fromHtml("Byly vytvořeny následujicí vlastní entity: <br>")
                                    . HTMLUtils::createUlList($ulListData, false), FlashMessages::SUCCESS);
                                $pages = $parsedIndexJSON["pages"];
                                foreach ($pages as $page) {
                                    $pageEntity = new Page();
                                    $pageEntity->name = $page["name"];
                                    $pageEntity->route = $page["route"];
                                    $pageEntity->description = $page["description"];
                                    $pageEntity->output_content = $page["output_content"];
                                    $pageEntity->output_type = $page["output_type"];
                                    $pageEntity->template_id = $id;
                                    $insertedPage = $this->pageRepository->insert($pageEntity->iterable());
                                    if (isset($page["variables"])) {
                                        $pageVars = $page["variables"];
                                        foreach ($pageVars as $var) {
                                            $varEntity = new PageVariable();
                                            $varEntity->input_type = $var["input_type"];
                                            if (isset($var["content"]) && $var["content"]) $varEntity->content = $var["content"];
                                            $varEntity->description = $var["description"];
                                            $varEntity->id_name = $var["id_name"];
                                            $varEntity->title = $var["title"];
                                            if (isset($var["required"]) && $var["required"]) $varEntity->required = $var["required"];
                                            $varEntity->page_id = $insertedPage->id;
                                            $this->pageVariableRepository->insert($varEntity->iterable());
                                        }
                                    }
                                }
                            });
                    } else {
                        throw new ValidationException("");
                    }
                } catch (ValidationException $validationException) {
                    $form->addError("Při načítání instalačního balíčku došlo k validační chybě. Instalační balíček je neplatný.");
                }
            }
        } else {
            $form->addError("Při dekompresi instalačního balíčku došlo k nečekané chybě.");
        }
    }
}