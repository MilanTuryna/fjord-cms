<?php


namespace App\Forms\SMTP;


use App\Forms\FlashMessages;
use App\Forms\FormOption;
use App\Forms\RepositoryForm;
use App\Forms\SMTP\Data\ServerFormData;
use App\Model\Database\DataRegulation;
use App\Model\Database\Repository;
use JetBrains\PhpStorm\Pure;
use Nette\Application\UI\Form;
use Nette\Application\UI\Presenter;

/**
 * Class ServerForm
 * @package App\Forms\SMTP
 */
class ServerForm extends RepositoryForm
{
    private Repository\SMTP\ServerRepository $serverRepository;

    /**
     * ServerForm constructor.
     * @param Presenter $presenter
     * @param Repository\SMTP\ServerRepository $serverRepository
     */
    #[Pure] public function __construct(Presenter $presenter, Repository\SMTP\ServerRepository $serverRepository)
    {
        parent::__construct($presenter, $serverRepository);
        $this->presenter = $presenter;
        $this->serverRepository = $serverRepository;
    }

    /**
     * @return Form
     */
    public function create(): Form
    {
       $form = parent::create();
       $form->addText("name", "Název SMTP serveru")
           ->setOption(FormOption::OPTION_NOTE, "Využijte jméno, díky kterému snadno identifikujete správný SMTP server.")
           ->setHtmlAttribute("placeholder", "")->setRequired(true);

       $form->addEmail("server_email", "Emailová adresa (odesílatel)")->setOption(FormOption::UPPER_LINE, 1)->setOption(FormOption::OPTION_NOTE, "Z tohoto emailu bude odeslán obsah zprávy kontaktního formuláře vč. reálného emailu odesílatele.")->setHtmlAttribute("placeholder", "john@example.com")->setRequired(true);
       $form->addPassword("server_password", "Heslo")->setRequired(true);
       $form->addText("server_host", "Hostitel serveru")->setHtmlAttribute("placeholder", "vase-firma.cz")->setRequired(true);
        $form->addEmail("receiver_email", "Emailová adresa (příjemce)")->setOption(FormOption::UPPER_LINE,1)
            ->setOption(FormOption::OPTION_NOTE, "Na tento a dále i v administračním výpisu budete příjimat odeslané zprávy.")->setHtmlAttribute("placeholder", "john@example.com")->setRequired(false);

        $form->addSubmit("submit", "Vytvořit nový SMTP server");
       return $form;
    }

    public function success(Form $form, ServerFormData $serverFormData): void {
        if(!$serverFormData->receiver_server_email) {
            $this->presenter->flashMessage("Z důvodu nevyplnění byl jako emailový účet příjemce přijat email odesílatele", FlashMessages::INFO);
        }
    }
}