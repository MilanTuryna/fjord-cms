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
 * Class EditServerForm
 * @package App\Forms\SMTP
 */
class EditServerForm extends ServerForm
{
    #[Pure] public function __construct(Presenter $presenter, private Repository\SMTP\ServerRepository $serverRepository, private int $server_id)
    {
        parent::__construct($presenter, $serverRepository);
    }

    /**
     * @return Form
     */
    public function create(): Form {
        $form = parent::create();
        $activeRow = $this->serverRepository->findById($this->server_id);
        return $this::createEditForm($form, $activeRow);
    }

    /**
     * @throws InvalidLinkException
     * @throws AbortException
     */
    public function success(Form $form, ServerFormData &$serverFormData): void
    {
        parent::success($form, $serverFormData);
        $this->successTemplate($form, $serverFormData->iterable(), new FormMessage("SMTP server byl úspěšně aktualizován.", "SMTP server byl úspěšně aktualizován."), new FormRedirect("this"), $this->server_id);
    }
}