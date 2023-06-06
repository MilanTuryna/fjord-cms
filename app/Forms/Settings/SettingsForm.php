<?php


namespace App\Forms\Settings;


use App\Forms\FormMessage;
use App\Forms\FormOption;
use App\Forms\FormRedirect;
use App\Forms\RepositoryForm;
use App\Forms\Settings\Data\SettingsFormData;
use App\Model\Database\DataRegulation;
use App\Model\Database\Repository;
use JetBrains\PhpStorm\Pure;
use Nette\Application\AbortException;
use Nette\Application\UI\Form;
use Nette\Application\UI\InvalidLinkException;
use Nette\Application\UI\Presenter;

/**
 * Class SettingsForm
 * @package App\Forms\Settings
 */
class SettingsForm extends RepositoryForm
{
    // for comparison purposes
    const INPUTS_LABELS = [
        "app_name" => "Název aplikace",
        "app_author" => "Vlastníci webu",
        "app_keywords" => "Klíčová slova",
    ];

    private Repository\Settings\GlobalSettingsRepository $settingsRepository;

    /**
     * SettingsForm constructor.
     * @param Presenter $presenter
     * @param Repository\Settings\GlobalSettingsRepository $repository
     * @param FormRedirect $formRedirect
     * @param int $admin_id
     */
    public function __construct(protected Presenter $presenter, Repository\Settings\GlobalSettingsRepository $repository, private FormRedirect $formRedirect, private int $admin_id)
    {
        parent::__construct($this->presenter, $repository);

        $this->settingsRepository = $repository;
    }

    public function create(): \Nette\Application\UI\Form
    {
        $form = parent::create();
        $actualSettings = $this->settingsRepository->getActualSettings();

        $form->addText("app_name", "Název webu")
            ->setMaxLength(DataRegulation::TITLE)
            ->setDefaultValue($actualSettings->app_name ?? "")->setRequired(true);
        $form->addText("app_author", "Vlastníci webu")
            ->setMaxLength(DataRegulation::TITLE)
            ->setDefaultValue($actualSettings->app_author ?? "")->setRequired(true);
        $form->addText("app_keywords", "Klíčová slova")
            ->setOption(FormOption::OPTION_NOTE, "Bez diakritiky, ve formátu: music, play, split, sound")
            ->setMaxLength(DataRegulation::DESCRIPTION)
            ->setDefaultValue($actualSettings->app_keywords ?? "")->setRequired(true);
        $form->addSubmit("submit", "Aktualizovat změny");
        return $form;
    }

    /**
     * @param Form $form
     * @param SettingsFormData $data
     * @throws AbortException
     * @throws InvalidLinkException
     */
    public function success(Form $form, SettingsFormData $data): void {
        $data->admin_id = $this->admin_id;
        $this->successTemplate($form, $data, new FormMessage(
            "Nastavení bylo úspěšně aktualizováno a ve všech službách aplikováno.",
            "Vyskytla se neznámá chyba v databázi a nebylo možné aktualizovat nastavení."));
    }
}