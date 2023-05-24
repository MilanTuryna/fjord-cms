<?php


namespace App\Forms\Dynamic;


use App\Forms\Dynamic\Data\EntityFormData;
use App\Forms\FormRedirect;
use App\Model\Database\Repository\Dynamic\AttributeRepository;
use App\Model\Database\Repository\Dynamic\EntityRepository;
use Nette\Application\AbortException;
use Nette\Application\UI\Form;
use Nette\Application\UI\Presenter;
use ReflectionException;

/**
 * Class EditEntityForm
 * @package App\Forms\Dynamic
 */
class EditEntityForm extends EntityForm
{
    private EntityFormData $oldEntityFormData;

    /**
     * @throws ReflectionException
     */
    public function __construct(public Presenter $presenter, private int $entityId, private EntityRepository $entityRepository, private AttributeRepository $attributeRepository)
    {
        parent::__construct($this->presenter);

        $this->oldEntityFormData = EntityFormData::generateFullEntity($this->entityId, $this->entityRepository, $this->attributeRepository);
    }

    /**
     * @return Form
     */
    public function create(): Form
    {
        $form = parent::create();
        $form->setDefaults($this->oldEntityFormData);
        $form['submit']->setCaption("Aktualizovat změny");
        return $form;
    }

    /**
     * @param Form $form
     * @param EntityFormData $data
     * @throws AbortException
     */
    public function success(Form $form, EntityFormData $data): void {
        $entityRow = $this->buildEntity($data);
        $updatedEntity = $this->entityRepository->updateById($this->entityId, $entityRow->iterable());
        foreach ($data->attributes as $attr_id => $attribute) {
            $this->attributeRepository->updateById($attr_id, $attribute->iterable());
        }
        $this->presenter->redirect("this");
    }
}