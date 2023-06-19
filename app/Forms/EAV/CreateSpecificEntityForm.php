<?php

namespace App\Forms\EAV;

use App\Forms\FormMessage;
use App\Forms\FormRedirect;
use App\Model\Database\EAV\EAVRepository;
use JetBrains\PhpStorm\Pure;
use Nette\Application\AbortException;
use Nette\Application\UI\Form;
use Nette\Application\UI\InvalidLinkException;
use Nette\Application\UI\Presenter;

/**
 * Class CreateSpecificEntityForm
 * @package App\Forms\EAV
 */
class CreateSpecificEntityForm extends SpecificEntityForm
{
    #[Pure] public function __construct(protected Presenter $presenter,protected EAVRepository $EAVRepository, private FormRedirect $formRedirect)
    {
        parent::__construct($this->presenter, $this->EAVRepository);
    }

    /**
     * @throws InvalidLinkException
     * @throws AbortException
     */
    public function success(Form $form, array $data): void {
        $this->successTemplate($form, $data, new FormMessage("Záznam byl úspěšně vytvořen.", "Záznam nemohl být z neznámého důvodu vytvořen."), $this->formRedirect, null, [], true);
    }
}