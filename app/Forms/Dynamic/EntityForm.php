<?php


namespace App\Forms\Dynamic;


use App\Forms\Dynamic\Data\AttributeData;
use App\Forms\Dynamic\Data\EntityFormData;
use App\Forms\Form;;

use App\Forms\FormOption;
use App\Model\Database\Repository\Dynamic\Entity\DynamicEntity;
use Nette\Forms\Container;
use Nette\Utils\DateTime;

/**
 * Class EntityForm
 * @package App\Forms\Dynamic
 */
class EntityForm extends Form
{
    /**
     * @param int $minCopies
     * @return \Nette\Application\UI\Form
     */
    public function create(int $minCopies = 1): \Nette\Application\UI\Form
    {
        $form = parent::create();
        $form->addText("entity_name", "Název entity/položky")
            ->setRequired(true);
        $form->addText(DynamicEntity::menu_item_name, "Název pro navigaci")->setOption(FormOption::OPTION_NOTE, "Uživatelsky přívětivý název označujicí správu dané entity. Např. 'Správa článků'");
        $form->addText("entity_description", "Popis položky")->setRequired(true);
        $maxAttributes = 50;
        $attrMultiplier = $form->addMultiplier("attributes", function (Container $container, \Nette\Forms\Form $form) use(&$attrMultiplier) {
            $container->addText(AttributeData::id_name, "Jmenný identifikátor")->setOption(FormOption::OPTION_NOTE, "Například 'name'")->setOption(FormOption::MULTIPLIER_PARENT, "attributes")->setRequired(true);
            $container->addText(AttributeData::title, "Název atributu")->setOption(FormOption::OPTION_NOTE, "Uživatelsky přívětivý název atributu (např. 'Název článku')")->setOption(FormOption::MULTIPLIER_PARENT, "attributes")->setRequired(true);
            $container->addSelect(AttributeData::data_type, "Datový typ", AttributeData::DATA_TYPES)->setOption(FormOption::MULTIPLIER_PARENT, "attributes")->setRequired(true);
            $container->addSelect(AttributeData::input_type, "Typ vstupu (inputu)", AttributeData::INPUT_TYPES)->setOption(FormOption::MULTIPLIER_PARENT, "attributes")->setRequired(true);
            $container->addCheckbox(AttributeData::enabled_wysiwyg, "Povolit pokročilý editor")
                ->setOption(FormOption::OPTION_NOTE, "WYSIWYG editor umožňujicí práci s textem, odsazování, nadpisy atd. Bude použito jen v případě vhodné kombinace s typem inputu.")
                ->setOption(FormOption::MULTIPLIER_PARENT, "attributes");
            $container->addText(AttributeData::description, "Popis atributu")->setOption(FormOption::MULTIPLIER_PARENT, "attributes");
            $container->addText(AttributeData::placeholder, "HTML Placeholder")->setOption(FormOption::OPTION_NOTE, "Vyjadřuje náhledový text před rozkliknutím inputu")->setOption(FormOption::MULTIPLIER_PARENT, "attributes");
            // If AttributeData::generate_value and ::preset_val then use prese_val;
            $container->addSelect(AttributeData::generate_value, "Vygenerované hodnoty",
                AttributeData::GENERATED_VALUES)->setRequired(false)->setOption(FormOption::MULTIPLIER_PARENT, "attributes")->setPrompt("Vyber generovanou hodnotu");
            $container->addTextarea(AttributeData::preset_value, "Přednastavení hodnoty")->setOption(FormOption::OPTION_NOTE, "V případě využití přednastavené hodnoty v kombinaci s datovým typem překladu, využijte prosím JSON dle struktrury uvedené v dokumentaci FjordCMS.")->setOption(FormOption::MULTIPLIER_PARENT, "attributes")->setRequired(false);
            $container->addCheckbox(AttributeData::required, "Je atribut povinný?")
                ->setOption(FormOption::OPTION_NOTE, "V případě povolení WYSIWYG editoru nedoporučujeme nastavovat tuto hodnotu na ANO.")
                ->setOption(FormOption::MULTIPLIER_PARENT, "attributes")->setRequired(false);
            $container->addCheckbox(AttributeData::hide_in_list, "Skrýt v hlavním výpisu")->setRequired(false)->setOption(FormOption::MULTIPLIER_PARENT, "attributes");
            $attrMultiplier->addRemoveButton("Odebrat atribut")->addClass('btn btn-danger');
        }, $minCopies, $maxAttributes);
        $attrMultiplier->addCreateButton("Přidat krok")->addClass('btn btn-dark w-100');
        $form->addSubmit("submit", "Vytvořit novou entitu");
        return $form;
    }

    /**
     * @param EntityFormData $data
     * @return DynamicEntity
     */
    protected function buildEntity(EntityFormData $data): DynamicEntity
    {
        $entity = new DynamicEntity();
        $entity->name = $data->entity_name;
        $entity->description = $data->entity_description;
        $entity->menu_item_name = $data->menu_item_name;
        $entity->created = new DateTime();
        $entity->edited = new DateTime();
        return $entity;
    }
}