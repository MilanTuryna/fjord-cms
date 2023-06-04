<?php


namespace App\Presenters\Admin;


// BasePresenter because AdminBasePresenter is verifying session automatically
use App\Forms\Auth\SignInForm;
use App\Model\Security\Auth\AdminAuthenticator;
use App\Presenters\BasePresenter;
use Nette\Application\AbortException;
use Nette\Application\UI\Form;

/**
 * Class AuthPresenter
 * @package App\Presenters\Admin
 */
class AuthPresenter extends BasePresenter
{
    private AdminAuthenticator $adminAuthenticator;

    /**
     * AdminAuthPresenter constructor.
     * @param AdminAuthenticator $adminAuthenticator
     */
    public function __construct(AdminAuthenticator $adminAuthenticator) {
        parent::__construct();

        $this->adminAuthenticator = $adminAuthenticator;
    }

    /**
     * @throws AbortException
     */
    public function startup() {
        parent::startup();
        if($this->adminAuthenticator->getId()) $this->redirect(":Admin:Overview:home");
    }

    /**
     * @return Form
     */
    public function createComponentSignInForm(): Form {
        return (new SignInForm($this, $this->adminAuthenticator, ":Admin:Overview:home"))->create();
    }
}