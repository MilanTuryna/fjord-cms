<?php


namespace App\Presenters\Admin;


use App\Forms\FlashMessages;
use App\Model\Security\Auth\Exceptions\LoggedOutException;
use App\Presenters\AdminBasePresenter;
use Nette\Application\AbortException;

/**
 * Class OverviewPresenter
 * @package App\Presenters\Admin
 */
class OverviewPresenter extends AdminBasePresenter
{
    public function renderHome() {
        
    }

    /**
     * @throws AbortException
     */
    public function actionLogout() {
        try {
            $this->adminAuthenticator->logout();
            $this->flashMessage("Byl jsi úspěšně odhlášen. Pro další manipulaci s rozhráním, se přihlaš.", FlashMessages::SUCCESS);
            $this->redirect(":Admin:Auth:login");
        } catch (LoggedOutException $e) {
            $this->flashMessage("Nemůžeš se odhlašovat, když už jsi odhlášený.", FlashMessages::ERROR);
            $this->redirect(":Admin:Auth:login");
        }
    }
}