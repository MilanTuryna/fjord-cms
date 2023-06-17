<?php


namespace App\Presenters\Admin\Internal;


use App\Forms\FormMessage;
use App\Forms\FormRedirect;
use App\Forms\SMTP\CreateServerForm;
use App\Forms\SMTP\EditServerForm;
use App\Model\Admin\Permissions\Specific\AdminPermissions;
use App\Model\Database\Repository\SMTP\Entity\Mail;
use App\Model\Database\Repository\SMTP\MailRepository;
use App\Model\Database\Repository\SMTP\ServerRepository;
use App\Model\Security\Auth\AdminAuthenticator;
use App\Presenters\AdminBasePresenter;
use JetBrains\PhpStorm\NoReturn;
use Nette\Application\AbortException;
use Nette\Application\UI\Form;
use Nette\Application\UI\Multiplier;

/**
 * Class SMTPPresenter
 * @package App\Presenters\Admin\Internal
 */
class SMTPPresenter extends AdminBasePresenter
{
    /**
     * SMTPPresenter constructor.
     * @param AdminAuthenticator $adminAuthenticator
     * @param ServerRepository $serverRepository
     * @param MailRepository $mailRepository
     * @param string $permissionNode
     */
    public function __construct(AdminAuthenticator $adminAuthenticator, private ServerRepository $serverRepository, private MailRepository $mailRepository, string $permissionNode = AdminPermissions::ADMIN_FULL)
    {
        parent::__construct($adminAuthenticator, $permissionNode);
    }

    public function renderView(int $id) {
        $this->template->server = $this->serverRepository->findById($id);
        $this->template->mails = $this->mailRepository->findByColumn(Mail::server_id, $id);
    }

    /**
     * @param int $server_id
     * @param int $id
     */
    public function renderViewMail(int $server_id, int $id) {
        $this->template->server = $this->serverRepository->findById($server_id);
        $this->template->mail = $this->mailRepository->findById($id);
    }

    /**
     * @throws AbortException
     */
    #[NoReturn] public function actionRemoveMail(int $server_id, int $id): void {
        $this->prepareActionRemove($this->mailRepository, $id, new FormMessage("Daný e-mail byl úspěšně vymazán z databáze (tzn. z výpisu).", "Daný e-mail nemohl být z neznámhéo důvodu vymazán z databáze."), new FormRedirect("view", [$server_id]));
    }

    /**
     * @param int $id
     * @throws AbortException
     */
    #[NoReturn] public function actionRemove(int $id): void {
        $this->prepareActionRemove($this->serverRepository, $id, new FormMessage("Daný SMTP server byl úspěšně odstraněn. Zkontrolujte si prosím, zda máte všechny zveřejněné formuláře na webu funkční.", "Daný SMTP server nemohl být z neznámého důvodu odstraněn."), new FormRedirect("list"));
    }

    /**
     * @return Form
     */
    public function createComponentCreateServerForm(): Form {
        return (new CreateServerForm($this, $this->serverRepository, new FormRedirect("view",  [FormRedirect::LAST_INSERT_ID])))->create();
    }

    /**
     * @return Multiplier
     */
    public function createComponentEditServerForm(): Multiplier {
        return new Multiplier(function ($server_id) {
            return (new EditServerForm($this, $this->serverRepository, (int)$server_id, new FormRedirect("remove", [$server_id])))->create();
        });
    }
}