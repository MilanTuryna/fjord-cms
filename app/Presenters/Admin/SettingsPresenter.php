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
            $comparisonList[] = new InputComparison($label, [$settingsRow->{$input}, $this->settings->{$input}]);
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