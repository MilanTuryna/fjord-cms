<?php

namespace App\Forms\Installation;

use App\Forms\FormOption;
use App\Forms\Installation\Select\DatabaseSystemSelect;
use App\Forms\Installation\Select\PresentationTypeSelect;
use App\Forms\Installation\Select\SpecializationSelect;
use Contributte\FormWizard\Wizard;
use Nette\Application\UI\Form;
use Nette\Http\Session;

class InstallationWizardForm extends Wizard
{

    /**
     * @param Session $session
     */
    public function __construct(Session $session)
    {
        parent::__construct($session, stepNames: [
            1 => "Vytvoření nového projektu",
            2 => "Nastavení administračního panelu",
            3 => "Výběr šablony",
            4 => "Instalace modulů",
            5 => "Databázové připojení",
            6 => "Shrnutí"
        ]);
    }

    public function startup(): void
    {
        $this->skipStepIf(3, function (array $values): bool { // skip template settings if admin chose start system without template
            return isset($values[1]) && $values[1]['presentation_type'] === PresentationTypeSelect::WITHOUT_TEMPLATE;
        });
    }

    // Creating a new project
    public function createStep1(): Form
    {
        $form = $this->createForm();

        $form->addText("name", "Název projektu")->setRequired();
        $form->addText("description", "Popis projektu")->setRequired();
        $form->addSelect("specialization", "Zaměření (obor projektu)", SpecializationSelect::selectBox())->setRequired();
        $form->addSelect("presentation_type", "Typ webové prezentace", PresentationTypeSelect::selectBox())
            ->setRequired();
        $form->addText("reason_for_usage", "Důvod použití FjordCMS")->setRequired(false);

        $author = $form->addContainer("author");
        $author->addEmail("email", "Email autora")->setRequired();
        $author->addText("name", "Jméno autora")->setRequired();

        $form->addSubmit(self::NEXT_SUBMIT_NAME, "Další krok");
        return $form;
    }

    // individual settings of administration panel
    public function createStep2(): Form
    {
        $form = $this->createForm();

        $colors = $form->addContainer("colors");
            $colors->addText("primary_color", "Primární barva")->setHtmlAttribute("type", "color");
            $colors->addText("secondary_color", "Sekundární barva")->setHtmlAttribute("type", "color");

        $form->addSubmit(self::PREV_SUBMIT_NAME, "Zpět");
        $form->addSubmit(self::NEXT_SUBMIT_NAME, "Další krok");

        return $form;
    }

    // uploading new template
    public function createStep3(): Form
    {
        $form = $this->createForm();
        $form->addUpload(".ZIP nahrané šablony")->setOption(FormOption::OPTION_NOTE, "Šablona bude zkontrolována zda odpovídá požadavkům webového systému.");

        $form->addSubmit(self::PREV_SUBMIT_NAME, "Zpět");
        $form->addSubmit(self::NEXT_SUBMIT_NAME, "Další krok");

        return $form;
    }

    // installation of modules
    public function createStep4(): Form
    {
        $form = $this->createForm();
        // inner logic to future
        $form->addSubmit(self::PREV_SUBMIT_NAME, "Zpět");
        $form->addSubmit(self::NEXT_SUBMIT_NAME, "Další krok");

        return $form;
    }

    // setting database credentials
    public function createStep5(): Form
    {
        $form = $this->createForm();
        $form->addSelect("system", "Databázový systém", DatabaseSystemSelect::selectBox())->setRequired(true);
        $form->addInteger("port", "Databázový port")
            ->setDefaultValue(3306)
            ->setMaxLength(5);
        $form->addText("hostname", "Hostitel")->setRequired(true);
        $form->addText("username", "Uživatel")->setRequired(true);
        $form->addPassword("password", "Heslo")->setRequired(false); // not required for the reason

        $form->addSubmit(self::PREV_SUBMIT_NAME, "Zpět");
        $form->addSubmit(self::NEXT_SUBMIT_NAME, "Další krok");

        return $form;
    }

    // summary
    public function createStep6(): Form
    {
        $form = $this->createForm();
        // summary todo
        $form->addSubmit(self::FINISH_SUBMIT_NAME, "Uložit a nainstalovat")->setOption(FormOption::BUTTON_DANGER, 1);
    }
}