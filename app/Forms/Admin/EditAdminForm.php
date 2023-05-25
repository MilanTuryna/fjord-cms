<?php


namespace App\Forms\Admin;


use App\Forms\Admin\Data\AdminFormData;
use App\Forms\FormMessage;
use App\Model\Admin\Permissions\Specific\AdminPermissions;
use App\Model\Admin\Permissions\Utils;
use App\Model\Database\Repository\Admin\AccountRepository;
use App\Model\Database\Repository\Admin\Entity\Account;
use JetBrains\PhpStorm\Pure;
use Nette\Application\AbortException;
use Nette\Application\UI\Form;
use Nette\Application\UI\InvalidLinkException;
use Nette\Application\UI\Presenter;
use Nette\Database\Table\ActiveRow;

/**
 * Class EditAdminForm
 * @package App\Forms\Admin
 */
class EditAdminForm extends AdminForm
{
    private int $admin_id;

    /**
     * EditAdminForm constructor.
     * @param Presenter $presenter
     * @param AccountRepository $accountRepository
     * @param int $admin_id
     */
    #[Pure] public function __construct(Presenter $presenter, AccountRepository $accountRepository, int $admin_id)
    {
        parent::__construct($presenter, $accountRepository);

        $this->admin_id = $admin_id;
    }

    /**
     * @return Form
     */
    public function create(): Form
    {
        $form = parent::create();
        /**
         * @var Account|ActiveRow $administrator
         */
        $administrator = $this->repository->findById($this->admin_id);
        $defaultValues = [];
        $userPerms = Utils::listToArray($administrator->permissions);
        foreach ((new AdminPermissions())->getAllNodes() as $permission) {
            if (in_array($permission, $userPerms)) array_push($defaultValues, $permission);
        }
        $form->addCheckboxList(AdminFormData::permissions_array, "Oprávnění", AdminPermissions::selectBox())->setDefaultValue($defaultValues);
        return $this::createEditForm($form, $administrator, "Aktualizovat změny", [
            AdminFormData::permissions_array, AdminFormData::password
        ]);
    }

    /**
     * @param Form $form
     * @param AdminFormData $data
     * @throws AbortException
     * @throws InvalidLinkException
     */
    public function success(\Nette\Application\UI\Form $form, AdminFormData $data) {
        $data->permissions = Utils::arrayToUnparsedList($data->permissions_array);
        $this->successTemplate($form, $data, new FormMessage("Informace o daném administrátorovi byly úspěšně změněny.", "Informace o daném administrátorovi nemohli být z neznámého důvodu změněny."), null, $this->admin_id);
    }
}