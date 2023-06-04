<?php


namespace App\Presenters\Admin\Internal;


use App\Forms\Dynamic\CreateEntityForm;
use App\Forms\Dynamic\EditEntityForm;
use App\Forms\FormMessage;
use App\Forms\FormRedirect;
use App\Model\Admin\Permissions\Utils;
use App\Model\Database\EAV\DynamicEntityFactory;
use App\Model\Database\Repository\Dynamic\AttributeRepository;
use App\Model\Database\Repository\Dynamic\Entity\DynamicAttribute;
use App\Model\Database\Repository\Dynamic\EntityRepository;
use App\Model\Database\Repository\Dynamic\ValueRepository;
use App\Model\Security\Auth\AdminAuthenticator;
use App\Presenters\AdminBasePresenter;
use JetBrains\PhpStorm\NoReturn;
use Nette\Application\AbortException;
use Nette\Application\UI\Form;
use Nette\Application\UI\Multiplier;

class EAVPresenter extends AdminBasePresenter
{
    /**
     * EAVPresenter constructor.
     * @param AdminAuthenticator $adminAuthenticator
     * @param EntityRepository $entityRepository
     * @param DynamicEntityFactory $dynamicEntityFactory
     * @param AttributeRepository $attributeRepository
     * @param ValueRepository $valueRepository
     * @param string $permissionNode
     */
    public function __construct(AdminAuthenticator $adminAuthenticator, public EntityRepository $entityRepository, public DynamicEntityFactory $dynamicEntityFactory, public AttributeRepository $attributeRepository, public ValueRepository $valueRepository, string $permissionNode = Utils::SPECIAL_WITHOUT_PERMISSION)
    {
        parent::__construct($adminAuthenticator, $permissionNode);
    }

    public function renderList() {
        $this->template->entities = $this->entityRepository->findAll()->fetchAll();
    }

    public function renderView(int $id) {
        $this->template->entity = $this->entityRepository->findById($id);
        $this->template->attributes = $this->attributeRepository->findByColumn(DynamicAttribute::entity_id, $id);
        $this->template->valuesCount = $this->valueRepository->findByColumn(DynamicAttribute::entity_id, $id)->count("id");
    }

    /**
     * @throws AbortException
     */
    #[NoReturn] public function actionRemove(int $id): void {
        $this->prepareActionRemove($this->entityRepository, $id, new FormMessage("Daná entita byla úspěšně odstraněna ze systému vč. všech jejích hodnot.", "Daná entita nemohla být z neznámého důvodu odstraněna ze systému."), "list");
    }

    /**
     * @return Form
     */
    public function createComponentCreateEntityForm(): Form {
        return (new CreateEntityForm($this, $this->dynamicEntityFactory, new FormRedirect("list")))->create();
    }

    /**
     * @return Multiplier
     */
    public function createComponentEditEntityForm(): Multiplier {
        return new Multiplier(function (int $entityId) {
            return (new EditEntityForm($this, $entityId, $this->entityRepository, $this->attributeRepository))->create();
        });
    }
}