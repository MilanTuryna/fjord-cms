<?php


namespace App\Presenters\Admin\Internal;


use App\Forms\FormMessage;
use App\Forms\FormRedirect;
use App\Forms\Template\InstallTemplateForm;
use App\Model\Admin\Permissions\Specific\AdminPermissions;
use App\Model\Admin\Permissions\Utils;
use App\Model\Database\EAV\DynamicEntityFactory;
use App\Model\Database\Repository\Admin\Entity\AccessLog;
use App\Model\Database\Repository\Dynamic\EntityRepository;
use App\Model\Database\Repository\Template\AuthorRepository;
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
     * @param AuthorRepository $authorRepository
     * @param string $permissionNode
     */
    public function __construct(AdminAuthenticator $adminAuthenticator, private TemplateRepository $templateRepository, private TemplateUploadDataProvider $templateUploadDataProvider, private DynamicEntityFactory $dynamicEntityFactory, private AuthorRepository $authorRepository, string $permissionNode = AdminPermissions::DEVELOPER_SETTINGS)
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
            $authors[$template->author_id] = $template->related("author_id")->fetch();
        }
        $this->template->authors = $authors;
    }

    /**
     * @param int $id
     * @return void
     */
    public function renderView(int $id): void {
        $templateRow = $this->template->templateRow = $this->templateRepository->findById($id);
        $this->template->templateAuthor = $templateRow->related("author_id")->fetch();
    }

    /**
     * @return Form
     */
    public function createComponentInstallTemplateForm(): Form {
        return (new InstallTemplateForm($this, $this->templateRepository, $this->templateUploadDataProvider,
            $this->dynamicEntityFactory, $this->authorRepository, new FormRedirect("view", [FormRedirect::LAST_INSERT_ID])))->create();
    }

}