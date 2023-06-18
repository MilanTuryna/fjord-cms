<?php


namespace App\Forms\Dynamic;


use App\Forms\Dynamic\Data\EntityFormData;
use App\Forms\FlashMessages;
use App\Forms\FormRedirect;
use App\Model\Database\EAV\DynamicEntityFactory;
use App\Model\Database\Repository\Dynamic\Entity\DynamicEntity;
use JetBrains\PhpStorm\NoReturn;
use JetBrains\PhpStorm\Pure;
use Nette\Application\AbortException;
use Nette\Application\UI\Form;
use Nette\Application\UI\Presenter;
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
     * @throws AbortException
     */
    #[NoReturn] public function success(Form $form, EntityFormData &$data): void {
        $data->attributes = $form->getHttpData()["attributes"];
        $entity = $this->buildEntity($data);
        $entityId = $this->dynamicEntityFactory->createEntity($entity, $data->attributes);
        $this->presenter->flashMessage("Entita byla úpěšně vytvořena", FlashMessages::SUCCESS);
        $this->redirect->presenter($this->presenter, $entityId);
    }
}