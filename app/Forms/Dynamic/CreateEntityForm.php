<?php


namespace App\Forms\Dynamic;


use App\Forms\Dynamic\Data\EntityFormData;
use App\Forms\FlashMessages;
use App\Forms\FormRedirect;
use App\Model\Database\EAV\DynamicEntityFactory;
use App\Model\Database\EAV\Exceptions\InvalidAttributeException;
use App\Model\Database\Repository\Dynamic\Entity\DynamicEntity;
use JetBrains\PhpStorm\NoReturn;
use JetBrains\PhpStorm\Pure;
use Nette\Application\AbortException;
use Nette\Application\UI\Form;
use Nette\Application\UI\Presenter;
use Nette\Database\UniqueConstraintViolationException;
use ReflectionException;
use Tracy\Debugger;
use Tracy\Dumper;
use Tracy\Helpers;

/**
 * Class CreateEntityForm
 * @package App\Forms\Dynamic
 */
class CreateEntityForm extends EntityForm
{
    /**
     * CreateEntityForm constructor.
     * @param Presenter $presenter
     * @param DynamicEntityFactory $dynamicEntityFactory
     * @param FormRedirect $redirect
     */
    #[Pure] public function __construct(Presenter $presenter, private DynamicEntityFactory $dynamicEntityFactory, private FormRedirect $redirect)
    {
        parent::__construct($presenter);
    }

    /**
     * @throws AbortException|ReflectionException
     */
    #[NoReturn] public function success(Form $form, EntityFormData &$data): void {
        $data->attributes = $form->getHttpData()["attributes"];
        $entity = $this->buildEntity($data);
        try {
            $entityId = $this->dynamicEntityFactory->createEntity($entity, $data->attributes);
            $this->presenter->flashMessage("Entita byla úpěšně vytvořena", FlashMessages::SUCCESS);
            $this->redirect->presenter($this->presenter, $entityId);
        } catch (UniqueConstraintViolationException|InvalidAttributeException $exception) {
            if($exception instanceof InvalidAttributeException) {
                $form->addError($exception->getMessage());
            } else {
                $form->addError("Název dynamické entity se shoduje s již vytvořenou dynamickou entitou. Opravte to.");
            }
        }
    }
}