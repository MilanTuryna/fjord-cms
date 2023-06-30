<?php


namespace App\Presenters;

use App\Forms\FlashMessages;
use App\Forms\FormMessage;
use App\Forms\FormRedirect;
use App\Model\Admin\Permissions\Utils;
use App\Model\Database\IRepository;
use App\Model\Database\Repository;
use App\Model\Database\Repository\Settings\GlobalSettingsRepository;
use App\Model\Security\Auth\AdminAuthenticator;
use JetBrains\PhpStorm\NoReturn;
use Nette\Application\AbortException;
use Nette\Database\Table\ActiveRow;

class AdminBasePresenter extends BasePresenter
{
    protected AdminAuthenticator $adminAuthenticator;

    /**
     * @var GlobalSettingsRepository @inject
     */
    public GlobalSettingsRepository $settingsRepository;

    /**
     * @var Repository\Admin\AccountRepository @inject
     */
    public Repository\Admin\AccountRepository $accountRepository;

    /**
     * @var Repository\Dynamic\EntityRepository @inject
     */
    public Repository\Dynamic\EntityRepository $entityRepository;

    /**
     * @var Repository\Template\TemplateRepository @inject
     */
    public Repository\Template\TemplateRepository $_templateRepository;

    /**
     * @var ActiveRow|null|Repository\Admin\Entity\Account
     */
    protected ActiveRow|Repository\Admin\Entity\Account|null $admin;


    protected ActiveRow|Repository\Settings\Entity\GlobalSettings|null $settings;
    protected ActiveRow|Repository\Template\Entity\Template|null $usedTemplate = null;

    private string $permissionNode;

    /**
     * AdminBasePresenter constructor.
     * @param AdminAuthenticator $adminAuthenticator
     * @param string $permissionNode
     */
    public function __construct(AdminAuthenticator $adminAuthenticator, string $permissionNode = Utils::SPECIAL_WITHOUT_PERMISSION)
    {
        parent::__construct();

        $this->adminAuthenticator = $adminAuthenticator;
        $this->permissionNode = $permissionNode;
    }

    protected function setIfCurrentEntity($entityName) {
        $this->template->currentEntity = $entityName;
    }

    /**
     * @throws AbortException
     */
    public function startup(): void
    {
        parent::startup();
        $this->settings = $this->settingsRepository->getActualSettings();

        if(!$this->adminAuthenticator->getId()) {
            $this->flashMessage("Před vstupem do administrace systému se musíš přihlásit.", FlashMessages::ERROR);
            $this->redirect(":Admin:Auth:login");
        }
        $this->admin = $this->adminAuthenticator->getUser();
        $adminEntity = new Repository\Admin\Entity\Account($this->admin->username, $this->admin->first_name, $this->admin->surname, $this->admin->email, $this->admin->password, $this->admin->permissions, $this->admin->created,$this->admin->id);
        if(!$adminEntity->getPermissionMap()[$this->permissionNode]) {
            $this->flashMessage("Pro vstup do této části administrace je zapotřebí vyšší oprávnění.", FlashMessages::ERROR);
            $this->redirect(":Admin:Overview:home");
        }
        $this->usedTemplate = $this->_templateRepository->findByColumn(Repository\Template\Entity\Template::used, 1)->fetch();
        $this->template->admin = $adminEntity;
        $this->enableFlashes();
    }

    /**
     * @param Repository $repository
     * @param int|string $id
     * @param FormMessage $formMessage
     * @param string|FormRedirect|null $route
     * @throws AbortException
     */
    #[NoReturn] public function prepareActionRemove(IRepository $repository, int|string $id, FormMessage $formMessage, string|FormRedirect|null $route) {
        $deleted = $repository->deleteById($id);
        if($deleted) {
            $this->flashMessage($formMessage->success, FlashMessages::SUCCESS);
        } else {
            $this->flashMessage($formMessage->error, FlashMessages::ERROR);
        }
        if($route) {
            if($route instanceof FormRedirect) {
                $this->redirect($route->route, $route->args);
            }
            $this->redirect($route);
        }
    }

    public function beforeRender()
    {
        $this->setIfCurrentEntity(null);
        $this->template->settings = $this->settings;
        $this->template->usedTemplate = $this->usedTemplate;
        $this->template->dynamicEntities = $this->usedTemplate ? $this->entityRepository->findByColumn(Repository\Dynamic\Entity\DynamicEntity::generated_by, $this->usedTemplate->dirname)->fetchAll() : [];
        $this->template->generalEntities = $this->entityRepository->findByColumn(Repository\Dynamic\Entity\DynamicEntity::generated_by, "")->fetchAll() ?? [];
        $this->template->activeWysiwyg = false;
    }

    /**
     * @throws AbortException
     */
    protected function onlyFullPermission() {
        if($this->admin->permissions != "*") { // if admin has full permissions then it will be always only *
            $this->flashMessage("Na zobrazení této části administrace nemáte oprávnění!", FlashMessages::ERROR);
            $this->redirect(":Admin:Overview:home");
        }
    }
}