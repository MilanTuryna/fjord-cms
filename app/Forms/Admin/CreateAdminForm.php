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
use Nette\Security\Passwords;
use stdClass;

class CreateAdminForm extends AdminForm
{
    private FormRedirect $formRedirect;

    /**
     * CreateAdminForm constructor.
     * @param Presenter $presenter
     * @param AccountRepository $accountRepository
     * @param Passwords $passwords
     * @param FormRedirect $redirect
     */
    #[Pure] public function __construct(Presenter $presenter, AccountRepository $accountRepository, private Passwords $passwords, FormRedirect $redirect)
    {
        parent::__construct($presenter, $accountRepository, $this->passwords);

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
     * @param stdClass $data
     * @throws AbortException
     * @throws InvalidLinkException
     */
    public function success(Form $form, stdClass &$data): void {
        parent::success($form, $data);
        $this->successTemplate($form, (array)$data, new FormMessage("Administrátorský účet byl úspěšně vytvořen.", "Admniistrátorský účet nemohl být z nějakého důvodu vytvořen."),
            $this->formRedirect);
    }
}