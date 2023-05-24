<?php


namespace App\Forms\Admin;


use App\Forms\Form;
use App\Forms\RepositoryForm;
use App\Model\Database\DataRegulation;

/**
 * Class AdminForm
 * @package App\Forms\Admin
 */
abstract class AdminForm extends RepositoryForm
{
    /**
     * @return \Nette\Application\UI\Form
     */
    public function create(): \Nette\Application\UI\Form
    {
        $form = parent::create();
        $form->addText("username", "Uživatelské jméno")->setMaxLength(DataRegulation::TITLE)->setRequired(true);
        $form->addEmail("email", "Emailová adresa")->setHtmlAttribute("placeholder", "example@fjordcms.com")->setMaxLength(DataRegulation::EMAIl_LENGTH)->setRequired(true);
        $form->addPassword("password", "Heslo")->setRequired(true);
        $form->addSubmit("submit", "Vytvořit nový účet");
        //$form->addCheckboxList("permissions_array", "Oprávnění", AdminPermissions::selectBox()); implemented it in children
        return $form;
    }
}