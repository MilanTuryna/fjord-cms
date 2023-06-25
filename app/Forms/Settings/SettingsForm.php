<?php


namespace App\Forms\Settings;


use App\Forms\FormMessage;
use App\Forms\FormOption;
use App\Forms\FormRedirect;
use App\Forms\RepositoryForm;
use App\Forms\Settings\Data\SettingsFormData;
use App\Forms\Universal\Comparison\InputComparison;
use App\Model\Constants\Countries;
use App\Model\Database\DataRegulation;
use App\Model\Database\Repository;
use JetBrains\PhpStorm\Pure;
use Nette\Application\AbortException;
use Nette\Application\UI\Form;
use Nette\Application\UI\InvalidLinkException;
use Nette\Application\UI\Presenter;
use Nette\Utils\DateTime;

/**
 * Class SettingsForm
 * @package App\Forms\Settings
 */
class SettingsForm extends RepositoryForm
{
    // for comparison purposes
    const INPUTS_LABELS = [ // ["title", "input_type|InputComparison::TYPE_INPUT|InputComparison::TYPE_ARRAY"]
        "app_name" => ["Název aplikace"],
        "app_author" => ["Vlastníci webu"],
        "app_keywords" => ["Klíčová slova"],
        "languages" => ["Jazyky překladu", InputComparison::TYPE_ARRAY],
        "default_language" => ["Hlavní jazyk"],
    ];

    private Repository\Settings\GlobalSettingsRepository $settingsRepository;

    /**
     * SettingsForm constructor.
     * @param Presenter $presenter
     * @param Repository\Settings\GlobalSettingsRepository $repository
     * @param FormRedirect $formRedirect
     * @param int $admin_id
     */
    #[Pure] public function __construct(protected Presenter $presenter, Repository\Settings\GlobalSettingsRepository $repository, private FormRedirect $formRedirect, private int $admin_id)
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
        $form->addCheckboxList("_languages", "Jazyky webu", Countries::LANGUAGES)
            ->setOption(FormOption::OPTION_NOTE, "Zvolte všechny jazyky, ve kterých chcete překládat obsah webu.")->setRequired("Je nutné, aby alespoň jeden jazyk byl označen jako základní.");
        $form->addSelect("default_language", "Hlavní jazyk", Countries::LANGUAGES)->setOption(FormOption::OPTION_NOTE, "Vybraný jazyk bude použit jako hlavní.")->setRequired("Hlavní jazyk musí být vybrán.");
        if($actualSettings && $actualSettings->languages) {
            $form["_languages"]->setDefaultValue(explode(",", $actualSettings->languages));
        }
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
        $data->created = new DateTime();
        if(count($data->_languages) < 1) $form->addError("Je nutné, aby alespoň jeden jazyk byl označen jako základní.");
        $data->languages = implode(",", $data->_languages);
        $this->successTemplate($form, $data->iterable(), new FormMessage(
            "Nastavení bylo úspěšně aktualizováno a ve všech službách aplikováno.",
            "Vyskytla se neznámá chyba v databázi a nebylo možné aktualizovat nastavení."), new FormRedirect("this"));
    }
}