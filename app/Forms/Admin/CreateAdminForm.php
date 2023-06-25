<?php


namespace App\Forms\Admin;


use App\Forms\Admin\Data\AdminFormData;
use App\Forms\FormMessage;
use App\Forms\FormRedirect;
use App\Forms\RepositoryForm;
use App\Model\Admin\Permissions\Specific\AdminPermissions;
use App\Model\Admin\Permissions\Utils;
use App\Model\Database\Repository\Admin\AccountRepository;
use JetBrains\PhpStorm\Pure;
use Nette\Application\AbortException;
use Nette\Application\UI\Form;
use Nette\Application\UI\InvalidLinkException;
use Nette\Application\UI\Presenter;

class CreateAdminForm extends AdminForm
{
    private FormRedirect $formRedirect;

    /**
     * CreateAdminForm constructor.
     * @param Presenter $presenter
     * @param AccountRepository $accountRepository
     * @param FormRedirect $redirect
     */
    #[Pure] public function __construct(Presenter $presenter, AccountRepository $accountRepository, FormRedirect $redirect)
    {
        parent::__construct($presenter, $accountRepository);

        $this->formRedirect = $redirect;
    }

    public function create(): Form
    {
        $form = parent::create();
        $form->addCheckboxList(AdminFormData::permissions_array, "Oprávnění", AdminPermissions::selectBox());
        return $form;
    }

    /**
     * @param Form $form
     * @param AdminFormData $data
     * @throws AbortException
     * @throws InvalidLinkException
     */
    public function success(Form $form, AdminFormData $data) {
        $data->permissions = Utils::arrayToUnparsedList($data->permissions_array);
        $this->successTemplate($form, $data->iterable(true), new FormMessage("Administrátorský účet byl úspěšně vytvořen.", "Admniistrátorský účet nemohl být z nějakého důvodu vytvořen"),
            $this->formRedirect);
    }
}