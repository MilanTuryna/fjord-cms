<?php


namespace App\Forms\SMTP;


use App\Forms\FormMessage;
use App\Forms\FormOption;
use App\Forms\FormRedirect;
use App\Forms\SMTP\Data\ServerFormData;
use App\Model\Database\Repository;
use JetBrains\PhpStorm\Pure;
use Nette\Application\AbortException;
use Nette\Application\UI\Form;
use Nette\Application\UI\InvalidLinkException;
use Nette\Application\UI\Presenter;
use Nette\Forms\Controls\TextInput;

/**
 * Class EditServerForm
 * @package App\Forms\SMTP
 */
class EditServerForm extends ServerForm
{
    #[Pure] public function __construct(Presenter $presenter, private Repository\SMTP\ServerRepository $serverRepository, private int $server_id, private FormRedirect $deleteRoute)
    {
        parent::__construct($presenter, $serverRepository);
    }

    /**
     * @return Form
     */
    public function create(): Form {
        $form = parent::create();
        $activeRow = $this->serverRepository->findById($this->server_id);
        $form[ServerFormData::server_password]->setRequired(false);
        $form['submit']->setOption(FormOption::DELETE_LINK, $this->deleteRoute);
        return $this::createEditForm($form, $activeRow);
    }

    /**
     * @throws InvalidLinkException
     * @throws AbortException
     */
    public function success(Form $form, ServerFormData &$serverFormData): void
    {
        parent::success($form, $serverFormData);
        $exceptions = [];
        if(!$serverFormData->server_password) $exceptions[] = ServerFormData::server_password;
        $this->successTemplate($form, $serverFormData->iterable(), new FormMessage("SMTP server byl úspěšně aktualizován.", "SMTP server byl úspěšně aktualizován."), new FormRedirect("this"), $this->server_id, $exceptions);
    }
}