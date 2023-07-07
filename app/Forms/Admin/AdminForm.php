<?php


namespace App\Forms\Admin;


use App\Forms\Form;
use App\Forms\RepositoryForm;
use App\Model\Admin\Permissions\Utils;
use App\Model\Database\DataRegulation;
use App\Model\Database\IRepository;
use App\Model\Database\Repository\Admin\Entity\Account;
use JetBrains\PhpStorm\Pure;
use Nette\Application\UI\Presenter;
use Nette\Security\Passwords;
use Nette\Utils\DateTime;
use stdClass;

/**
 * Class AdminForm
 * @package App\Forms\Admin
 */
abstract class AdminForm extends RepositoryForm
{
    /**
     * AdminForm constructor.
     * @param Presenter $presenter
     * @param IRepository $repository
     * @param Passwords $passwords
     */
    #[Pure] public function __construct(Presenter $presenter, IRepository $repository, private Passwords $passwords)
    {
        parent::__construct($presenter,$repository);
        $this->presenter = $presenter;
    }

    /**
     * @return \Nette\Application\UI\Form
     */
    public function create(): \Nette\Application\UI\Form
    {
        $form = parent::create();
        $form->addText("username", "Uživatelské jméno")->setMaxLength(DataRegulation::TITLE)->setRequired(true);
        $form->addText(Account::first_name, "Křestné jméno")->setRequired(true);
        $form->addText(Account::surname, "Přijmení")->setRequired(true);
        $form->addEmail("email", "Emailová adresa")->setHtmlAttribute("placeholder", "example@fjordcms.com")->setMaxLength(DataRegulation::EMAIl_LENGTH)->setRequired(true);
        $form->addPassword("password", "Heslo")->setRequired(true);
        $form->addSubmit("submit", "Vytvořit nový účet");
        //$form->addCheckboxList("permissions_array", "Oprávnění", AdminPermissions::selectBox()); implemented it in children
        return $form;
    }

    /**
     * @param \Nette\Application\UI\Form $form
     * @param stdClass $data
     */
    public function success(\Nette\Application\UI\Form $form, stdClass &$data): void {
        $data->created = new DateTime();
        $data->permissions = Utils::arrayToUnparsedList($data->permissions_array);
        $data->password = $this->passwords->hash($data->password);
        unset($data->permissions_array);
    }
}