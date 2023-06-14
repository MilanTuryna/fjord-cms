<?php


namespace App\Presenters\Admin\Internal;


use App\Forms\FormMessage;
use App\Forms\Template\EditTemplateForm;
use App\Forms\Template\InstallTemplateForm;
use App\Model\Admin\Permissions\Specific\AdminPermissions;
use App\Model\Admin\Permissions\Utils;
use App\Model\Database\Repository\Admin\Entity\AccessLog;
use App\Model\Database\Repository\Template\AuthorRepository;
use App\Model\Database\Repository\Template\TemplateRepository;
use App\Model\Security\Auth\AdminAuthenticator;
use App\Presenters\AdminBasePresenter;
use JetBrains\PhpStorm\NoReturn;
use Nette\Application\AbortException;
use Nette\Application\UI\Form;
use Nette\Application\UI\Multiplier;


class TemplatePresenter extends AdminBasePresenter
{
    /***
     * @param AdminAuthenticator $adminAuthenticator
     * @param TemplateRepository $templateRepository
     * @param AuthorRepository $authorRepository
     * @param string $permissionNode
     */
    public function __construct(AdminAuthenticator $adminAuthenticator, private TemplateRepository $templateRepository, private AuthorRepository $authorRepository, string $permissionNode = AdminPermissions::DEVELOPER_SETTINGS)
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

    public function createComponentInstallTemplateForm(): Form {
        return (new InstallTemplateForm())->create();
    }

    public function createComponentEditTemplateForm(): Multiplier {
        return new Multiplier(function ($id) {
            return new EditTemplateForm();
        });
    }
}