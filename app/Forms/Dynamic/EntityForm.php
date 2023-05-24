<?php


namespace App\Forms\Dynamic;


use App\Forms\Dynamic\Data\AttributeData;
use App\Forms\Dynamic\Data\EntityFormData;
use App\Forms\Form;;

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
            $container->addText(AttributeData::name, "Název atributu")->setRequired(true);
            $container->addSelect(AttributeData::data_type, "Datový typ", AttributeData::DATA_TYPES)->setRequired(true);
            $container->addText(AttributeData::description, "Popis atributu");
            $container->addText(AttributeData::placeholder, "HTML Placeholder");
            // If AttributeData::generate_value and ::preset_val then use prese_val;
            $container->addSelect(AttributeData::generate_value, "Vygenerované hodnoty",
                AttributeData::GENERATED_VALUES)->setRequired(false)->setPrompt("Vyber generovanou hodnotu");
            $container->addSelect(AttributeData::preset_val, "Přednastavení hodnoty")->setRequired(false);
            $container->addCheckbox(AttributeData::required, "Je atribut povinný?")->setRequired(false);
            $attrMultiplier->addRemoveButton("Odebrat atribut")->addClass('btn btn-danger');
        }, 1, $maxAttributes);
        $attrMultiplier->createButton("Přidat krok")->addClass('btn btn-dark w-100');
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