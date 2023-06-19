<?php


namespace App\Presenters\Admin;


use App\Forms\FormRedirect;
use App\Forms\Settings\SettingsForm;
use App\Forms\Universal\Comparison\InputComparison;
use App\Model\Admin\Permissions\Specific\AdminPermissions;
use App\Model\Admin\Permissions\Utils;
use App\Model\Database\Repository\Settings\Entity\GlobalSettings;
use App\Model\Security\Auth\AdminAuthenticator;
use App\Presenters\AdminBasePresenter;
use Nette\Application\UI\Form;

class SettingsPresenter extends AdminBasePresenter
{
    /**
     * SettingsPresenter constructor.
     * @param AdminAuthenticator $adminAuthenticator
     * @param string $permissionNode
     */
    public function __construct(AdminAuthenticator $adminAuthenticator, string $permissionNode = AdminPermissions::ADMIN_FULL)
    {
        parent::__construct($adminAuthenticator, $permissionNode);
    }

    public function renderOverview() {
        $this->template->changeList = $this->settingsRepository->findAll()->order("created DESC");
        $this->template->administratorMap = $this->accountRepository->findAll()->fetchPairs("id");
    }

    /**
     * @param int $id
     */
    public function renderHistory(int $id) {
        /**
         * @var $settingsRow GlobalSettings
         */
        $settingsRow = $this->settingsRepository->findById($id);
        $this->template->settingsRow = $settingsRow;

        $comparisonList = [];
        foreach (SettingsForm::INPUTS_LABELS as $input => $label) {
           $inputComparison = new InputComparison($label[0], [$settingsRow->{$input}, $this->settings->{$input}]);
           if(isset($label[1])) $inputComparison->inputType = $label[1];
           $comparisonList[] = $inputComparison;
        }
        $comparisonList[] = new InputComparison("Zveřejnil/a", [
            $this->accountRepository->findById($settingsRow->admin_id)->username,
            $this->accountRepository->findById($this->settings->admin_id)->username
        ]);
        $this->template->comparisonList = $comparisonList;
    }

    /**
     * @return Form
     */
    public function createComponentSettingsForm(): Form {
        return (new SettingsForm($this, $this->settingsRepository, FormRedirect::this(), $this->admin->id))->create();
    }
}