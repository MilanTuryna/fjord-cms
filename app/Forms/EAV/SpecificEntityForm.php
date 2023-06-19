<?php

namespace App\Forms\EAV;

use App\Forms\Dynamic\Data\AttributeData;
use App\Forms\Form;
use App\Forms\FormOption;
use App\Forms\RepositoryForm;
use App\Model\Database\EAV\DataType;
use App\Model\Database\EAV\EAVRepository;
use App\Model\Database\Repository\Dynamic\Entity\DynamicAttribute;
use JetBrains\PhpStorm\Pure;
use Nette\Application\UI\Presenter;
use ReflectionException;

class SpecificEntityForm extends RepositoryForm
{
    /**
     * @param Presenter $presenter
     * @param EAVRepository $EAVRepository
     */
    #[Pure] public function __construct(protected Presenter $presenter, protected EAVRepository $EAVRepository)
    {
        parent::__construct($presenter, $EAVRepository);
    }

    /**
     * @return \Nette\Application\UI\Form
     * @throws ReflectionException
     */
    public function create(): \Nette\Application\UI\Form
    {
        $form = parent::create();
        $formAttributes = $this->EAVRepository->getEntityAttributesAssoc();
        foreach ($formAttributes as $attribute) {
            /**
             * @var $attribute array
             * @var $input
             */
            $attribute_o = new DynamicAttribute();
            $attribute_o->createFrom((object)$attribute);
            $attribute = $attribute_o;
            if($attribute->generate_value) continue;
            $input = match ($attribute->data_type) {
                DataType::INTEGER => $form->addInteger($attribute->id_name, $attribute->title),
                DataType::TRANSLATED_VALUE => $form->addText($attribute->id_name, $attribute->title),
                DataType::FLOAT => $form->addText($attribute->id_name, $attribute->title)->addRule(\Nette\Forms\Form::Float, `{$attribute->id_name} ({$attribute->title}) musí být číslo.`),
                DataType::BOOL => $form->addCheckbox($attribute->id_name, $attribute->title),
                DataType::STRING, DataType::ARBITRARY => $form->addText($attribute->id_name, $attribute->title), // TODO
            };
            if($attribute->placeholder) $input->setHtmlAttribute("placeholder", $attribute->placeholder);
            if($attribute->required) $input->setRequired();
            if($attribute->description) $input->setOption(FormOption::OPTION_NOTE, $attribute->description);
            if($attribute->preset_value) $input->setDefaultValue($attribute->preset_value);
        }
        $form->addSubmit("submit", "Vytvořit " . $this->EAVRepository->entityName);
        return $form;
    }
}