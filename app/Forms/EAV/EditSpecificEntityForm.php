<?php

namespace App\Forms\EAV;

use App\Forms\Dynamic\Data\AttributeData;
use App\Forms\Dynamic\Data\GeneratedValues;
use App\Forms\FormMessage;
use App\Forms\FormOption;
use App\Forms\FormRedirect;
use App\Model\Database\EAV\EAVRepository;
use App\Model\Database\Repository\Admin\AccountRepository;
use App\Model\Database\Repository\Admin\Entity\Account;
use App\Model\Database\Repository\Dynamic\Entity\DynamicAttribute;
use Nette\Application\AbortException;
use Nette\Application\UI\Form;
use Nette\Application\UI\InvalidLinkException;
use Nette\Application\UI\Presenter;
use Nette\Utils\ArrayHash;
use Nette\Utils\DateTime;
use ReflectionException;

class EditSpecificEntityForm extends SpecificEntityForm
{
    private ArrayHash $entityData;
    private array $attributes;

    /**
     * EditSpecificEntityForm constructor.
     * @param Presenter $presenter
     * @param EAVRepository $EAVRepository
     * @param string $rowUnique
     * @param int $admin_id
     * @param AccountRepository $accountRepository
     * @param FormRedirect $deleteRoute
     */
    public function __construct(protected Presenter $presenter, protected EAVRepository $EAVRepository, private string $rowUnique, int $admin_id, private AccountRepository $accountRepository, private FormRedirect $deleteRoute)
    {
        parent::__construct($this->presenter, $this->EAVRepository, $admin_id);

        $this->entityData = $this->EAVRepository->findByUnique($this->rowUnique);
        $this->attributes = $this->EAVRepository->getEntityAttributesAssoc();
    }

    /**
     * @return Form
     * @throws ReflectionException
     */
    public function create(): Form
    {
        $form = parent::create(); // TODO: Change the autogenerated stub
        $defaultValues = [];
        foreach ((array)$this->entityData as $k => $v) {
            if(isset($form[$k])) {
                $defaultValues[$k] = $v;
            } else {
                if($k !== "row_unique" && in_array($this->attributes[$k][DynamicAttribute::generate_value], array_keys(AttributeData::GENERATED_VALUES))) {
                    if(in_array($this->attributes[$k][DynamicAttribute::generate_value], [GeneratedValues::CREATED_ADMIN, GeneratedValues::EDITED_ADMIN])) {
                        $v = $this->accountRepository->findById($v)->{Account::username};
                    }
                    $form->addText("___" . $k, $this->attributes[$k][DynamicAttribute::title])->setOmitted(true)->setDisabled(true)->setDefaultValue($v);
                }
            }
        }
        $form->setDefaults($defaultValues);
        $form['submit']->setOption(FormOption::DELETE_LINK, $this->deleteRoute);
        return self::createEditForm($form, (object)$this->entityData, "Aktualizovat změny");
    }

    /**
     * @throws InvalidLinkException
     * @throws AbortException
     */
    public function success(Form $form, array &$data): void {
        foreach ($this->attributes as $generatedAttribute => $content) {
            if(in_array($generatedAttribute, $data)) continue;
            match ($content[DynamicAttribute::generate_value]) {
                GeneratedValues::EDITED => $data[$content[DynamicAttribute::id_name]] = new DateTime(),
                GeneratedValues::EDITED_ADMIN => $data[$content[DynamicAttribute::id_name]] = $this->admin_id,
                default => ""
            };
        }
        $this->successTemplate($form, $data, new FormMessage("Entita " . $this->EAVRepository->entityName . " byla úspěšně aktualizována.", "Entita " . $this->EAVRepository->entityName . " nemohla být z neznámého důvodu aktualizována."), new FormRedirect("this"), $this->rowUnique);
    }
}