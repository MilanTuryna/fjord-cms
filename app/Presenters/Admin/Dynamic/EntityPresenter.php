<?php


namespace App\Presenters\Admin\Dynamic;


use App\Forms\FormMessage;
use App\Model\Admin\Permissions\Specific\AdminPermissions;
use App\Model\Database\EAV\DynamicEntityFactory;
use App\Model\Database\EAV\EAVRepository;
use App\Model\Database\EAV\Exceptions\EntityNotFoundException;
use App\Model\Security\Auth\AdminAuthenticator;
use App\Presenters\AdminBasePresenter;
use JetBrains\PhpStorm\NoReturn;
use Nette\Application\AbortException;

class EntityPresenter extends AdminBasePresenter
{
    /**
     * @param AdminAuthenticator $adminAuthenticator
     * @param DynamicEntityFactory $dynamicEntityFactory
     * @param string $permissionNode
     */
    public function __construct(AdminAuthenticator $adminAuthenticator, protected DynamicEntityFactory $dynamicEntityFactory, string $permissionNode = AdminPermissions::DYNAMIC_ENTITY_EDIT,)
    {
        parent::__construct($adminAuthenticator, $permissionNode);
    }

    /**
     * @throws EntityNotFoundException
     */
    public function renderList(string $entityName) {
        $this->setIfCurrentEntity($entityName);
        $EAVRepository = $this->dynamicEntityFactory->getEntityRepository($entityName);
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
    }
}