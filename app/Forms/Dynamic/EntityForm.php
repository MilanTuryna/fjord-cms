<?php


namespace App\Forms\Dynamic;


use App\Forms\Dynamic\Data\AttributeData;
use App\Forms\Dynamic\Data\EntityFormData;
use App\Forms\Form;;

use App\Forms\FormOption;
use App\Model\Database\Repository\Dynamic\Entity\DynamicEntity;
use Nette\Forms\Container;

/**
 * Class EntityForm
 * @package App\Forms\Dynamic
 */
class EntityForm extends Form
{
    /**
     * @return \Nette\Application\UI\Form
     */
    public function create(): \Nette\Application\UI\Form
    {
        $form = parent::create();
        $form->addText("entity_name", "Název entity/položky")
            ->setRequired(true);
        $form->addText("entity_description", "Popis položky")->setRequired(true);
        $maxAttributes = 50;
        $attrMultiplier = $form->addMultiplier("attributes", function (Container $container, \Nette\Forms\Form $form) use(&$attrMultiplier) {
            $container->addText(AttributeData::name, "Název atributu")->setOption(FormOption::MULTIPLIER_PARENT, "attributes")->setRequired(true);
            $container->addSelect(AttributeData::data_type, "Datový typ", AttributeData::DATA_TYPES)->setOption(FormOption::MULTIPLIER_PARENT, "attributes")->setRequired(true);
            $container->addText(AttributeData::description, "Popis atributu")->setOption(FormOption::MULTIPLIER_PARENT, "attributes");
            $container->addText(AttributeData::placeholder, "HTML Placeholder")->setOption(FormOption::OPTION_NOTE, "Vyjadřuje náhledový text před rozkliknutím inputu")->setOption(FormOption::MULTIPLIER_PARENT, "attributes");
            // If AttributeData::generate_value and ::preset_val then use prese_val;
            $container->addSelect(AttributeData::generate_value, "Vygenerované hodnoty",
                AttributeData::GENERATED_VALUES)->setRequired(false)->setOption(FormOption::MULTIPLIER_PARENT, "attributes")->setPrompt("Vyber generovanou hodnotu");
            $container->addTextarea(AttributeData::preset_val, "Přednastavení hodnoty")->setOption(FormOption::OPTION_NOTE, "V případě využití přednastavené hodnoty v kombinaci s datovým typem překladu, využijte prosím JSON dle struktrury uvedené v dokumentaci FjordCMS.")->setOption(FormOption::MULTIPLIER_PARENT, "attributes")->setRequired(false);
            $container->addCheckbox(AttributeData::required, "Je atribut povinný?")->setOption(FormOption::MULTIPLIER_PARENT, "attributes")->setRequired(false);
            $attrMultiplier->addRemoveButton("Odebrat atribut")->addClass('btn btn-danger');
        }, 1, $maxAttributes);
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
        $entity->created = new \DateTime();
        $entity->last_edit = new \DateTime();
        return $entity;
    }
}