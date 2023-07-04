<?php


namespace App\Presenters\Admin\Administrator;


use App\Forms\Admin\CreateAdminForm;
use App\Forms\Admin\EditAdminForm;
use App\Forms\FormMessage;
use App\Forms\FormRedirect;
use App\Model\Admin\Permissions\Utils;
use App\Model\Database\Repository\Admin\AccessLogRepository;
use App\Model\Database\Repository\Admin\AccountRepository;
use App\Model\Database\Repository\Admin\Entity\AccessLog;
use App\Model\Security\Auth\AdminAuthenticator;
use App\Presenters\AdminBasePresenter;
use JetBrains\PhpStorm\NoReturn;
use Nette\Application\AbortException;
use Nette\Application\UI\Form;
use Nette\Application\UI\Multiplier;

/**
 * Class AccountPresenter
 * @package App\Presenters\Admin\Administrator
 */
class AccountPresenter extends AdminBasePresenter
{
    public function __construct(AdminAuthenticator $adminAuthenticator, public AccountRepository $accountRepository, private AccessLogRepository $accessLogRepository, string $permissionNode = Utils::SPECIAL_WITHOUT_PERMISSION)
    {
        parent::__construct($adminAuthenticator, $permissionNode);
    }

    public function renderList() {
        $this->template->administrators = $this->accountRepository->findAll()->fetchPairs("id");
        $this->template->accessLogs = $this->accessLogRepository->findAll()->order(AccessLog::created . " DESC")->limit(12);
        $this->template->accounts = $this->accountRepository->findAll()->fetchAll();
    }

    /**
     * @throws AbortException
     */
    #[NoReturn] public function renderRemove(int $id) {
        $this->prepareActionRemove($this->accountRepository, $id, new FormMessage("Danému administrátorovi byla úspěšně odebrána práva a přístup.", "Danému administrátorovi nebyla z neznámého důvodu odebrána práva ani přístup."), "list");
    }

    public function renderView(int $id) {
        $this->template->account = $this->accountRepository->findById($id);
    }

    /**
     * @return Form
     */
    public function createComponentCreateAdminForm(): Form {
        return (new CreateAdminForm($this, $this->accountRepository, $this->adminAuthenticator->getPasswords(),
            new FormRedirect(":Admin:Administrator:Account:view", [FormRedirect::LAST_INSERT_ID])))->create();
    }

    /**
     * @return Multiplier
     */
    public function createComponentEditAdminForm(): Multiplier {
        return new Multiplier(function ($adminId) {
            return (new EditAdminForm($this, $this->accountRepository,  $this->adminAuthenticator->getPasswords(), (int)$adminId))->create();
        });
    }
}