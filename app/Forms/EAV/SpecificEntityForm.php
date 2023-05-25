<?php

namespace App\Forms\EAV;

use App\Forms\Dynamic\Data\AttributeData;
use App\Forms\Form;
use App\Forms\FormOption;
use App\Forms\RepositoryForm;
use App\Model\Database\EAV\DataType;
use App\Model\Database\EAV\EAVRepository;
use App\Model\Database\Repository\Dynamic\Entity\DynamicAttribute;
use Nette\Application\UI\Presenter;

class SpecificEntityForm extends RepositoryForm
{
    /**
     * @param Presenter $presenter
     * @param EAVRepository $EAVRepository
     */
    public function __construct(protected Presenter $presenter, protected EAVRepository $EAVRepository)
    {
        parent::__construct($presenter, $EAVRepository);
    }

    /**
     * @return \Nette\Application\UI\Form
     */
    public function create(): \Nette\Application\UI\Form
    {
        $form = parent::create();
        $formAttributes = $this->EAVRepository->getEntityAttributesAssoc();
        foreach ($formAttributes as $attribute) {
            /**
             * @var $attribute DynamicAttribute|array
             * @var $input
             */
            if($attribute->generate_value) continue;
            switch ($attribute->data_type) {
                case DataType::INTEGER:
                    $input = $form->addInteger($attribute->name, $attribute->name);
                    break;
                case DataType::TRANSLATED_VALUE:
                    // TODO: vymyslet jak to bude s tou prekladanou hodnotou resp. jak bude zpracovana v samotnem formulari
                    $input = $form->addText($attribute->name, $attribute->name);
                    break;
                case DataType::FLOAT:
                    $input = $form->addText($attribute->name, $attribute->name)->addRule(\Nette\Forms\Form::Float, `{$attribute->name} musí být číslo.`);
                    break;
                case DataType::BOOL:
                    $input = $form->addCheckbox($attribute->name, $attribute->name);
                    break;
                case DataType::STRING:
                    $input = $form->addText($attribute->name, $attribute->name);
            }
            if($attribute->placeholder) $input->setHtmlAttribute("placeholder", $attribute->placeholder);
            if($attribute->required) $input->setRequired();
            if($attribute->description) $input->setOption(FormOption::OPTION_NOTE, $attribute->description);
            if($attribute->preset_value) $input->setDefaultValue($attribute->preset_value);
        }
        $form->addSubmit("submit", "Vytvořit " . $this->EAVRepository->entityName);
        return $form;
    }
}