<?php


namespace App\Presenters\Admin\Internal;


use App\Forms\FlashMessages;
use App\Forms\FormMessage;
use App\Forms\FormRedirect;
use App\Forms\Template\CreateJsonSchemaForm;
use App\Forms\Template\InstallTemplateForm;
use App\Forms\Template\Page\EditTemplatePageVariablesForm;
use App\Model\Admin\Permissions\Specific\AdminPermissions;
use App\Model\Database\EAV\DynamicEntityFactory;
use App\Model\Database\Repository\Template\AuthorRepository;
use App\Model\Database\Repository\Template\Entity\Page;
use App\Model\Database\Repository\Template\Entity\PageVariable;
use App\Model\Database\Repository\Template\Entity\Template;
use App\Model\Database\Repository\Template\PageRepository;
use App\Model\Database\Repository\Template\PageVariableRepository;
use App\Model\Database\Repository\Template\TemplateRepository;
use App\Model\FileSystem\Templating\TemplateUploadDataProvider;
use App\Model\Security\Auth\AdminAuthenticator;
use App\Presenters\AdminBasePresenter;
use JetBrains\PhpStorm\NoReturn;
use Nette\Application\AbortException;
use Nette\Application\UI\Form;
use Nette\Application\UI\Multiplier;


class TemplatePresenter extends AdminBasePresenter
{
    /**
     * @param AdminAuthenticator $adminAuthenticator
     * @param TemplateRepository $templateRepository
     * @param TemplateUploadDataProvider $templateUploadDataProvider
     * @param DynamicEntityFactory $dynamicEntityFactory
     * @param AuthorRepository $authorRepository
     * @param PageRepository $pageRepository
     * @param PageVariableRepository $pageVariableRepository
     * @param string $permissionNode
     */
    public function __construct(AdminAuthenticator $adminAuthenticator, private TemplateRepository $templateRepository,
                                private TemplateUploadDataProvider $templateUploadDataProvider, private DynamicEntityFactory $dynamicEntityFactory,
                                private AuthorRepository $authorRepository, private PageRepository $pageRepository, private PageVariableRepository $pageVariableRepository,
                                string $permissionNode = AdminPermissions::DEVELOPER_SETTINGS)
    {
        parent::__construct($adminAuthenticator, $permissionNode);
    }

    /**
     * @throws AbortException
     */
    #[NoReturn] public function actionRemove(int $id): void {
        $this->prepareActionRemove($this->templateRepository, $id, new FormMessage("Daná šablona byla úspěšně vymazána ze systému.", "Daná šablona nemohla být z neznámého důvodu vymázana ze systému."), "list");
    }

    public function renderList(): void {
        $templates = $this->template->templates = $this->templateRepository->findAll()->fetchAll();
        $authors = [];
        foreach ($templates as $template) {
            $authors[$template->author_id] = $template->related("fjord_template_author.id")->fetch();
        }
        $this->template->templates = $templates;
        $this->template->authors = $authors;
    }

    /**
     * @param int $id
     * @param mixed $value
     * @throws AbortException
     */
    #[NoReturn] public function actionEnable(int $id, mixed $value): void {
        $this->templateRepository->update([Template::used => 0]);
        $this->templateRepository->updateById($id, [
            Template::used => $value ? 1 : 0
        ]);
        $this->flashMessage(sprintf("Daná šablona byla úspěšně %s", !$value ? "vypnuta." : "zapnuta."), FlashMessages::SUCCESS);
        $this->redirect("view", $id);
    }

    /**
     * @param int $id
     * @return void
     */
    public function renderView(int $id): void {
        $templateRow = $this->template->templateRow = $this->templateRepository->findById($id);
        $this->template->template = $templateRow;
        $this->template->templateAuthor = $templateRow->related("fjord_template_author.id")->fetch();
        $this->template->pages = $this->pageRepository->findByColumn(Page::template_id, $templateRow->id);
    }

    public function renderViewPage(int $templateId, int $pageId): void {
        $this->template->page = $this->pageRepository->findById($pageId);
        $this->template->template = $this->_templateRepository->findById($templateId);
        $this->template->pageVariables = $this->pageVariableRepository->findByColumn(PageVariable::page_id, $pageId)->fetchAll();
    }

    /**
     * @return Form
     */
    public function createComponentInstallTemplateForm(): Form {
        return (new InstallTemplateForm($this, $this->templateRepository, $this->templateUploadDataProvider,
            $this->dynamicEntityFactory, $this->authorRepository, $this->pageRepository, $this->pageVariableRepository, new FormRedirect("view", [FormRedirect::LAST_INSERT_ID])))->create();
    }

    /**
     * @return Multiplier
     */
    public function createComponentEditTemplatePageVariablesForm(): Multiplier {
        return (new Multiplier(function ($pageId) {
            return (new EditTemplatePageVariablesForm($this, $this->pageVariableRepository, (int)$pageId, new FormRedirect("this")))->create();
        }));
    }

    /**
     * @return Form
     */
    public function createComponentGenerateSchemaForm(): Form {
        return (new CreateJsonSchemaForm($this, $this->dynamicEntityFactory))->create();
    }
}