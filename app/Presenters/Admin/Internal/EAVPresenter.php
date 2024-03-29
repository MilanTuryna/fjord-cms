<?php


namespace App\Presenters\Admin\Internal;


use App\Forms\Dynamic\CreateEntityForm;
use App\Forms\Dynamic\EditEntityForm;
use App\Forms\FormMessage;
use App\Forms\FormRedirect;
use App\Model\Admin\Permissions\Specific\AdminPermissions;
use App\Model\Admin\Permissions\Utils;
use App\Model\Database\EAV\DynamicEntityFactory;
use App\Model\Database\Repository\Dynamic\AttributeRepository;
use App\Model\Database\Repository\Dynamic\Entity\DynamicAttribute;
use App\Model\Database\Repository\Dynamic\EntityRepository;
use App\Model\Database\Repository\Dynamic\ValueRepository;
use App\Model\Http\Responses\PrettyJsonResponse;
use App\Model\Security\Auth\AdminAuthenticator;
use App\Presenters\AdminBasePresenter;
use JetBrains\PhpStorm\NoReturn;
use Nette\Application\AbortException;
use Nette\Application\Responses\JsonResponse;
use Nette\Application\Responses\TextResponse;
use Nette\Application\UI\Form;
use Nette\Application\UI\Multiplier;
use Nette\Utils\Json;
use Nette\Utils\JsonException;

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
    public function __construct(AdminAuthenticator $adminAuthenticator, public EntityRepository $entityRepository, public DynamicEntityFactory $dynamicEntityFactory, public AttributeRepository $attributeRepository, public ValueRepository $valueRepository,
                                string $permissionNode = AdminPermissions::DYNAMIC_ENTITY_ADMIN)
    {
        parent::__construct($adminAuthenticator, $permissionNode);
    }

    public function renderList() {
        $this->template->entities = $this->entityRepository->findAll()->fetchAll();
    }

    /**
     */
    public function renderView(int $id) {
        $dynEntity = $this->template->dynamicEntity = $this->entityRepository->findById($id);
        $dynArray = [
          "entity_name" => $dynEntity->name,
          "entity_description" => $dynEntity->description,
          "entity_item_name" => $dynEntity->menu_item_name,
        ];
        unset($dynArray["id"]); // todo
        $attributes = $this->template->attributes = $this->attributeRepository->findByColumn(DynamicAttribute::entity_id, $id)->fetchAll();
        foreach ($attributes as $attribute) {
            if(!isset($dynArray["attributes"])) $dynArray["attributes"] = [];
            $arr = $attribute->toArray();
            unset($arr["id"]);
            unset($arr["entity_id"]);
            $dynArray["attributes"][] = $arr;
        }
        $this->template->valuesCount = $this->valueRepository->findByColumn(DynamicAttribute::entity_id, $id)->count("id");
        $this->template->entityJSON = stripslashes(json_encode($dynArray, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
    }

    /**
     * @throws AbortException
     */
    #[NoReturn] public function renderEntitySchema(): void {
        $response = new PrettyJsonResponse(   $this->dynamicEntityFactory->getEntitiesSchema(), null, true, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        $this->sendResponse($response);
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
        return new Multiplier(function ($entityId) {
            return (new EditEntityForm($this, (int)$entityId, $this->entityRepository, $this->attributeRepository,
                new FormRedirect("remove", [$entityId])))->create();
        });
    }
}