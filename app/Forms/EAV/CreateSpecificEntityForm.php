<?php

namespace App\Forms\EAV;

use App\Forms\Dynamic\Data\GeneratedValues;
use App\Forms\FormMessage;
use App\Forms\FormRedirect;
use App\Model\Database\EAV\EAVRepository;
use App\Model\Database\Repository\Dynamic\Entity\DynamicAttribute;
use JetBrains\PhpStorm\Pure;
use Nette\Application\AbortException;
use Nette\Application\UI\Form;
use Nette\Application\UI\InvalidLinkException;
use Nette\Application\UI\Presenter;
use Nette\Utils\DateTime;

/**
 * Class CreateSpecificEntityForm
 * @package App\Forms\EAV
 */
class CreateSpecificEntityForm extends SpecificEntityForm
{
    /**
     * CreateSpecificEntityForm constructor.
     * @param Presenter $presenter
     * @param EAVRepository $EAVRepository
     * @param FormRedirect $formRedirect
     * @param int $admin_id
     */
    #[Pure] public function __construct(protected Presenter $presenter,protected EAVRepository $EAVRepository, private FormRedirect $formRedirect, int $admin_id)
    {
        parent::__construct($this->presenter, $this->EAVRepository, $admin_id);
    }

    /**
     * @throws InvalidLinkException
     * @throws AbortException
     */
    public function success(Form $form, array &$data): void {
        $attributes = $this->EAVRepository->getEntityAttributesAssoc();
        foreach ($attributes as $generatedAttribute => $content) {
            if(in_array($generatedAttribute, $data)) continue;
            match ($content[DynamicAttribute::generate_value]) {
                GeneratedValues::CREATED, GeneratedValues::EDITED => $data[$content[DynamicAttribute::id_name]] = new DateTime(),
                GeneratedValues::EDITED_ADMIN, GeneratedValues::CREATED_ADMIN => $data[$content[DynamicAttribute::id_name]] = $this->admin_id,
                default => "",
            };
        }
        $this->successTemplate($form, $data, new FormMessage("Záznam byl úspěšně vytvořen.", "Záznam nemohl být z neznámého důvodu vytvořen."), $this->formRedirect, null, [], true);
    }
}