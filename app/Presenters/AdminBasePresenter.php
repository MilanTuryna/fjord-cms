<?php


namespace App\Presenters;

use App\Forms\FlashMessages;
use App\Forms\FormMessage;
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
     * @var GlobalSettingsRepository @inject
     */
    public GlobalSettingsRepository $accountRepository;

    protected ?ActiveRow $admin;


    protected ActiveRow|Repository\Settings\Entity\GlobalSettings|null $settings;

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

    /**
     * @throws AbortException
     */
    public function startup(): void
    {
        parent::startup();
        $this->settings = $this->settingsRepository->getActualSettings();

        if(!$this->adminAuthenticator->getId()) {
            $this->flashMessage("Před vstupem do dispečerské zóny, se musíš přihlásit.", FlashMessages::ERROR);
            $this->redirect(":Admin:Auth:login");
        }
        $this->admin = $this->adminAuthenticator->getUser();
        $adminEntity = new Repository\Admin\Entity\Account($this->admin->username, $this->admin->email, $this->admin->password, $this->admin->permissions, $this->admin->id);
        if(!$adminEntity->getPermissionMap()[$this->permissionNode]) {
            $this->flashMessage("Pro vstup do této části administrace je zapotřebí vyšší oprávnění.", FlashMessages::ERROR);
            $this->redirect(":Admin:Overview:home");
        }
        $this->template->admin = $adminEntity;
        $this->enableFlashes();
    }

    /**
     * @param Repository $repository
     * @param int $id
     * @param FormMessage $formMessage
     * @param string $route
     * @throws AbortException
     */
    #[NoReturn] public function prepareActionRemove(IRepository $repository, int $id, FormMessage $formMessage, string $route) {
        $deleted = $repository->deleteById($id);
        if($deleted) {
            $this->flashMessage($formMessage->success, FlashMessages::SUCCESS);
        } else {
            $this->flashMessage($formMessage->error, FlashMessages::ERROR);
        }
        $this->redirect($route);
    }

    public function beforeRender()
    {
        $this->template->settings = $this->settings;
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