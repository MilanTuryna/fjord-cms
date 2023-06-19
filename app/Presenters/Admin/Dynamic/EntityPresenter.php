<?php


namespace App\Presenters\Admin\Dynamic;


use App\Forms\Dynamic\CreateEntityForm;
use App\Forms\Dynamic\EditEntityForm;
use App\Forms\EAV\CreateSpecificEntityForm;
use App\Forms\EAV\SpecificEntityForm;
use App\Forms\FormMessage;
use App\Forms\FormRedirect;
use App\Model\Admin\Permissions\Specific\AdminPermissions;
use App\Model\Database\EAV\DynamicEntityFactory;
use App\Model\Database\EAV\EAVRepository;
use App\Model\Database\EAV\Exceptions\EntityNotFoundException;
use App\Model\Database\Repository\Dynamic\AttributeRepository;
use App\Model\Database\Repository\Dynamic\Entity\DynamicAttribute;
use App\Model\Database\Repository\Dynamic\Entity\DynamicEntity;
use App\Model\Security\Auth\AdminAuthenticator;
use App\Presenters\AdminBasePresenter;
use JetBrains\PhpStorm\NoReturn;
use Nette\Application\AbortException;
use Nette\Application\UI\Form;
use Nette\Application\UI\Multiplier;

/**
 * Class EntityPresenter
 * @package App\Presenters\Admin\Dynamic
 */
class EntityPresenter extends AdminBasePresenter
{
    /**
     * @param AdminAuthenticator $adminAuthenticator
     * @param DynamicEntityFactory $dynamicEntityFactory
     * @param AttributeRepository $attributeRepository
     */
    public function __construct(AdminAuthenticator $adminAuthenticator, protected DynamicEntityFactory $dynamicEntityFactory, private AttributeRepository $attributeRepository)
    {
        parent::__construct($adminAuthenticator, AdminPermissions::DYNAMIC_ENTITY_ADMIN);
    }

    /**
     * @throws EntityNotFoundException
     */
    public function renderList(string $entityName) {
        $this->setIfCurrentEntity($entityName);
        $EAVRepository = $this->dynamicEntityFactory->getEntityRepository($entityName);
        $this->template->entityName = $entityName;
        $entity = $this->template->entity = $this->entityRepository->findByColumn(DynamicEntity::name, $entityName)->fetch();
        $this->template->attributes = $this->attributeRepository->findByColumn(DynamicAttribute::entity_id, $entity->id)->fetchAll();
        $this->template->rows = $EAVRepository->findAll();
    }

    /**
     * @throws EntityNotFoundException
     */
    public function renderView(string $entityName, string $rowUnique) {
        $this->setIfCurrentEntity($entityName);
        $EAVRepository = $this->dynamicEntityFactory->getEntityRepository($entityName);
        $this->template->entityName = $entityName;
        $this->template->rowUnique = $rowUnique;
        $this->template->row = $EAVRepository->findByUnique($rowUnique);
    }

    /**
     * @throws AbortException|EntityNotFoundException
     */
    #[NoReturn] public function actionRemove(string $entityName, string $rowUnique) {
        $this->setIfCurrentEntity($entityName);
        $EAVRepository = $this->dynamicEntityFactory->getEntityRepository($entityName);
        $this->prepareActionRemove($EAVRepository, $rowUnique, new FormMessage("$entityName (ID: $rowUnique) byl úspěšně odebrán.", "$entityName (ID: $rowUnique) nebyl z neznámého důvodu odebrán."), "list");
    }

    public function renderNew(string $entityName) {
        $this->setIfCurrentEntity($entityName);
        $this->template->entityName = $entityName;
        $this->template->entity = $this->entityRepository->findByColumn(DynamicEntity::name, $entityName)->fetch();
    }

    /**
     * @return Multiplier
     */
    public function createComponentCreateSpecificEntityForm(): Multiplier
    {
        return new Multiplier(function ($entityId) {
            $entityRepository = $this->dynamicEntityFactory->getEntityRepositoryById($entityId);
            return (new CreateSpecificEntityForm($this, $this->dynamicEntityFactory->getEntityRepositoryById($entityId), new FormRedirect("list",
                [$entityRepository->entityName])))->create();
        });
    }
}