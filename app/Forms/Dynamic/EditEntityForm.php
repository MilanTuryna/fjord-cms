<?php


namespace App\Forms\Dynamic;


use App\Forms\Dynamic\Data\EntityFormData;
use App\Forms\FlashMessages;
use App\Forms\FormRedirect;
use App\Model\Database\Repository\Dynamic\AttributeRepository;
use App\Model\Database\Repository\Dynamic\Entity\DynamicAttribute;
use App\Model\Database\Repository\Dynamic\EntityRepository;
use JetBrains\PhpStorm\NoReturn;
use Nette\Application\AbortException;
use Nette\Application\UI\Form;
use Nette\Application\UI\Presenter;
use Nette\Forms\Container;
use Nette\InvalidStateException;
use ReflectionException;

/**
 * Class EditEntityForm
 * @package App\Forms\Dynamic
 */
class EditEntityForm extends EntityForm
{
    private EntityFormData $oldEntityFormData;

    /**
     */
    public function __construct(public Presenter $presenter, private int $entityId, private EntityRepository $entityRepository, private AttributeRepository $attributeRepository)
    {
        parent::__construct($this->presenter);

        $this->oldEntityFormData = EntityFormData::generateFullEntity($this->entityId, $this->entityRepository, $this->attributeRepository);
    }

    /**
     * @param int $minCopies
     * @return Form
     */
    public function create(int $minCopies = 1): Form
    {
        $form = parent::create(count($this->oldEntityFormData->attributes) > 1 ? 0 : 1);
        $form["attributes"]->setValues($this->oldEntityFormData->attributes);
        $form->setDefaults($this->oldEntityFormData->iterable());
        $form['submit']->setCaption("Aktualizovat změny");
        return $form;
    }

    /**
     * @param Form $form
     * @param EntityFormData $data
     * @throws AbortException
     * @throws ReflectionException
     */
    #[NoReturn] public function success(Form $form, EntityFormData $data): void {
        $data->attributes = $form->getHttpData()["attributes"];
        bdump($this->oldEntityFormData);
        bdump($data);
        $entityRow = $this->buildEntity($data);
        $updatedEntity = $this->entityRepository->updateById($this->entityId, $entityRow->iterable());
        $changedAttribute = false;
        foreach ($data->attributes as $attr_id => $attribute) {
            if(str_starts_with($attr_id, EntityFormData::ROW_KEY_CHAR)) {
                $action = $this->attributeRepository->updateById((int)ltrim($attr_id, EntityFormData::ROW_KEY_CHAR), $attribute);
            } else {
                $attributeObject = new DynamicAttribute();
                $attributeObject->createFrom((object)$attribute);
                $action = $this->attributeRepository->addAttribute($this->entityId, $attributeObject);
            }
            if(!$changedAttribute) $changedAttribute = (bool)$action;
        }
        foreach ($this->oldEntityFormData->attributes as $attr_id => $oldAttributes) {
            if(str_starts_with($attr_id, EntityFormData::ROW_KEY_CHAR) && !array_key_exists($attr_id, $data->attributes)) {
                $this->attributeRepository->deleteById((int)ltrim($attr_id, EntityFormData::ROW_KEY_CHAR));
            }
        }
        $this->presenter->flashMessage("Entita byla úspěšně aktualizována.", FlashMessages::SUCCESS);
        if($changedAttribute) $this->presenter->flashMessage("Atributy entity byly též úspěšně aktualizovány.", FlashMessages::SUCCESS);
        $this->presenter->redirect("this");
    }
}