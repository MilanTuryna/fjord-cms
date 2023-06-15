<?php


namespace App\Forms\SMTP;


use App\Forms\FormMessage;
use App\Forms\FormRedirect;
use App\Forms\SMTP\Data\ServerFormData;
use App\Model\Database\Repository;
use JetBrains\PhpStorm\Pure;
use Nette\Application\AbortException;
use Nette\Application\UI\Form;
use Nette\Application\UI\InvalidLinkException;
use Nette\Application\UI\Presenter;

/**
 * Class CreateServerForm
 * @package App\Forms\SMTP
 */
class CreateServerForm extends ServerForm
{
    #[Pure] public function __construct(Presenter $presenter, Repository\SMTP\ServerRepository $serverRepository, private FormRedirect $formRedirect)
    {
        parent::__construct($presenter, $serverRepository);
    }

    /**
     * @param Form $form
     * @param ServerFormData $serverFormData
     * @throws AbortException
     * @throws InvalidLinkException
     */
    public function success(Form $form, ServerFormData $serverFormData): void
    {
        parent::success($form, $serverFormData);
        $this->successTemplate($form, $serverFormData->iterable(), new FormMessage("Daný SMTP byl úspěšně aktualizován.", "Daný SMTP nemohl být z neznámého důovdu aktualizován"), $this->formRedirect);
    }
}