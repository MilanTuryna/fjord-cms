<?php


namespace App\Forms\Template;


use App\Forms\Dynamic\Data\AttributeData;
use App\Forms\Form;
use App\Forms\FormOption;
use App\Forms\Template\Data\JsonSchemaFormData;
use App\Model\Database\EAV\DynamicEntityFactory;
use App\Model\Database\Repository\Template\Entity\PageVariable;
use App\Model\Templating\Schema\IndexJsonSchema;
use App\Model\UI\FlashMessages\GeneratedJsonSchema;
use App\Utils\FormatUtils;
use JetBrains\PhpStorm\Pure;
use Nette\Application\UI\Presenter;
use Nette\Forms\Controls\TextInput;
use Nette\Schema\Processor;
use Nette\Schema\ValidationException;
use Nette\Utils\Json;
use Tracy\Debugger;

/**
 * Class CreateTemplateForm
 * @package App\Forms\Template
 * Creating JSON like IndexJsonSchema
 */
class CreateJsonSchemaForm extends Form
{
    /**
     * CreateTemplateForm constructor.
     * @param Presenter $presenter
     * @param DynamicEntityFactory $dynamicEntityFactory
     */
    #[Pure] public function __construct(protected Presenter $presenter, private DynamicEntityFactory $dynamicEntityFactory)
    {
        parent::__construct($this->presenter);
    }

    /**
     * @return \Nette\Application\UI\Form
     */
    public function create(): \Nette\Application\UI\Form
    {
        $form = parent::create();
        $form->addTextArea("load_json", "Načíst JSON")->setOption(FormOption::BOTTOM_LINE, 1)->setOption(FormOption::OPTION_NOTE, "Určeno pro ty, kteří chtějí načíst již existujicí JSON a ten pomocí generátoru upravovat")->setRequired(false);
        $form->addText("title", "Název šablony")->setRequired(true);
        $form->addText("error404", "Cesta k stránce 404")->setOption(FormOption::OPTION_NOTE, "Relativní cesta ke stránce reprezentujicí 404 (Not Found) od souboru index.json")->setRequired(false);
        $form->addTextArea("description", "Popis šablony (max. 200 znaků)")->setRequired(false);
        $form->addText("author_name", "Autor šablony")->setHtmlAttribute("placeholder", "Pepa Novák")->setRequired(true);
        $form->addEmail("author_email", "Email autora")->setHtmlAttribute("placeholder", "ja@pepa-novak.cz")->setRequired(false);
        $form->addText("author_website", "Web autora")->addRule(\Nette\Application\UI\Form::URL, "Web autora musí být ve formátu URL adresy.")->setHtmlAttribute("placeholder", "www.pepa-novak.cz")->setRequired(false);
        $pages = $form->addMultiplier("pages", function (\Nette\Forms\Container $container, \Nette\Application\UI\Form $form) {
            $container->addText("name", "Název stránky")->setRequired(true)->setOption(FormOption::MULTIPLIER_PARENT, "pages");
            $container->addText("route", "Routa stránky")->setOption(FormOption::MULTIPLIER_PARENT, "pages")
                ->setOption(FormOption::OPTION_NOTE,
                    "Routa stránky je definována jako routovací maska viz. Nette")
                ->setRequired(true);
            $container->addText("description", "Popis stránky")->setRequired(false)->setOption(FormOption::MULTIPLIER_PARENT, "pages");
            $container->addTextArea("output_content", "Výstup/obsah stránky")->setRequired(true)->setOption(FormOption::MULTIPLIER_PARENT, "pages");
            $container->addSelect("output_type", "Typ výstupu", [
                "SRC" => "Zdrojový kód (nedoporučujeme)",
                "PATH" => "Relativní cesta k souborům"
            ])->setRequired(true)->setOption(FormOption::MULTIPLIER_PARENT, "pages")->setOption(FormOption::OPTION_NOTE, "Relativní cesta je brána jako cesta k uvedenému souboru od místa, kde bude uložen tento soubor.");
            $vars = $container->addMultiplier("variables", function (\Nette\Forms\Container $varContainer, \Nette\Application\UI\Form $varForm) {
                $varContainer->addText(PageVariable::id_name, "Identifikátor proměnné")
                    ->setOption(FormOption::MULTIPLIER_PARENT, "variables")
                    ->addRule(function ($s) {
                        if($s instanceof TextInput) return FormatUtils::validateInputName($s->getValue());
                        return FormatUtils::validateInputName($s);
                    }, "Identifikátor proměnné musí splňovat dané podmínky: bez čísel, anglickou abecedu (bez diakritiky), bez mezer")
                    ->setOption(FormOption::OPTION_NOTE, "Bez čísel, speciálních znaků a diakritiky")->setRequired(true);
                $varContainer->addText(PageVariable::title, "Název proměnné")->setRequired(true)->setOption(FormOption::MULTIPLIER_PARENT, "variables");
                $varContainer->addText(PageVariable::description, "Popis proměnné")->setRequired(false)->setOption(FormOption::MULTIPLIER_PARENT, "variables");
                $varContainer->addText(PageVariable::content, "Obsah proměnné")->setOption(FormOption::MULTIPLIER_PARENT, "variables")->setOption(FormOption::OPTION_NOTE, "Tento obsah nastaví základní hodnotu proměnné, předtím, než jí uživatel nastaví dle libosti.")->setRequired(false);
                $varContainer->addSelect(PageVariable::input_type, "Typ proměnné/inputu", AttributeData::INPUT_TYPES)->setOption(FormOption::MULTIPLIER_PARENT, "variables")->setRequired(true);
            },0,10)->setOption(FormOption::MULTIPLIER_PARENT, "pages");
            $vars->addCreateButton("Přidat proměnnou");
            $vars->addRemoveButton("Odebrat proměnnou");
        },1,10);
        $pages->addRemoveButton("Odebrat stránku");
        $pages->addCreateButton("Přidat stránku");
        $form->addSelect("choose_eav", "Výběr entit EAV", [
            JsonSchemaFormData::CHOOSE_EAV_JSON => "Zadat JSON kód ručně (viz. textarea níže)",
            JsonSchemaFormData::CHOOSE_EAV_LOAD => "Načíst ze systému"
        ])->setRequired(true);
        $form->addTextArea("eav_code", "JSON kód EAV entit")
            ->setOption(FormOption::OPTION_NOTE, "Vyplňte pouze v případě zadání JSON ručně")
            ->setRequired(false);
        $form->addText("version", "Verze šablony")->setRequired(true);
        bdump($form["choose_eav"]::class);
        $form->addSubmit("submit", "Vygenerovat schéma");
        return $form; // TODO: Change the autogenerated stub
    }

    /**
     * @param $firstVar
     * @param $secondVar
     * @param array $pages
     * @param array $eav
     * @param bool $fromJSON
     */
    public function loadJsonTo(&$firstVar, &$secondVar, array $pages, array $eav, bool $fromJSON) {
        $firstVar["title"] = $secondVar["title"];
        $firstVar["description"] = $secondVar["description"];
        if($secondVar["error404"]) $firstVar["error404"] = $secondVar["error404"];
        $firstVar["author"] = [];
        if($fromJSON) {
            if($secondVar["author"]["email"]) $firstVar["author_email"] = $secondVar["author"]["email"];
            if($secondVar["author"]["name"]) $firstVar["author_name"] = $secondVar["author"]["name"];
            if($secondVar["author"]["website"]) $firstVar["author_website"] = $secondVar["author"]["website"];
        } else {
            if($secondVar["author_email"]) $firstVar["author"]["email"] = $secondVar["author_email"];
            if($secondVar["author_name"]) $firstVar["author"]["name"] = $secondVar["author_name"];
            if($secondVar["author_website"]) $firstVar["author"]["website"] = $secondVar["author_website"];
        }
        if(($fromJSON ? $secondVar["author"]["website"] : $secondVar["author_website"])) $firstVar["author"]["website"] = $fromJSON ? $secondVar["author"]["website"] : $secondVar["author_website"];
        $firstVar["pages"] = $pages;
        $firstVar["eav"] = $eav;
        $firstVar["version"] = $secondVar["version"];
    }

    /**
     * @param \Nette\Application\UI\Form $form
     * @param array $data
     */
    public function success(\Nette\Application\UI\Form $form, array $data) {
        if(isset($data["load_json"]) && $data["load_json"] != "") {
            $jsonDecoded =json_decode($data["load_json"], true);
            $data = [];
            $this->loadJsonTo($data, $jsonDecoded, $jsonDecoded["pages"], $jsonDecoded["eav"], true);
            $form->setDefaults($data);
        } else {
            $httpData = $form->getHttpData();
            $jsonSchema = [];
            $this->loadJsonTo($jsonSchema, $data, $httpData["pages"],
                ($data["choose_eav"] === JsonSchemaFormData::CHOOSE_EAV_JSON && isset($data["eav_code"]) && trim($data["eav_code"]) != "" ?
                    json_decode($data["eav_code"], true)
                    : $this->dynamicEntityFactory->getEntitiesSchema()), false);

            $processor = new Processor();
            $validationSchema = IndexJsonSchema::getSchema();
            try {
                if($processor->process($validationSchema, $jsonSchema)) {
                    $converted = stripslashes(json_encode($jsonSchema, JSON_PRETTY_PRINT));
                    $this->presenter->flashMessage(new GeneratedJsonSchema($converted));
                } else {
                    throw new ValidationException("Při procesu validace vygenerovaného schématu došlo k neznámé chybě.");
                }
            } catch (ValidationException $validationException) {
                if(Debugger::detectDebugMode()) throw $validationException;
                $form->addError("Při procesu validace vygenerovaného schématu došlo k neznámé chybě.");
            }
        }
    }
}