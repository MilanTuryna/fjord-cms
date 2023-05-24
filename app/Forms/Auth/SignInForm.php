<?php


namespace App\Forms\Auth;


use App\Forms\FlashMessages;
use App\Forms\Form;
use App\Model\Security\Auth\Exceptions\BadCredentialsException;
use App\Model\Security\Auth\IAuthenticator;
use JetBrains\PhpStorm\Pure;
use Nette\Application\AbortException;
use Nette\Application\UI\Presenter;
use stdClass;

/**
 * Class SignInForm
 * @package App\Forms\Auth
 */
class SignInForm extends Form
{

    /**
     * SignInForm constructor.
     * @param Presenter $presenter
     * @param IAuthenticator $authenticator
     * @param string $redirect
     */
    #[Pure] public function __construct(protected Presenter $presenter, protected IAuthenticator $authenticator, protected string $redirect)
    {
        parent::__construct($this->presenter);
    }

    /**
     * @return \Nette\Application\UI\Form
     */
    public function create(): \Nette\Application\UI\Form {
        $form = parent::create();
        $form->addEmail("email", "Email")
            ->setRequired(true);
        $form->addPassword("password", "Heslo")->setRequired(true);
        $form->addSubmit("submit", "Odeslat")->setRequired(true);
        return $form;
    }

    /**
     * @param \Nette\Application\UI\Form $form
     * @param stdClass $values
     * @return void
     * @throws AbortException
     */
    public function success(\Nette\Application\UI\Form $form, stdClass $values): void {
        try {
            $this->authenticator->login([$values->email, $values->password]);
            $this->presenter->flashMessage("Byl/a jsi úspěšně autorizován a přihlášen!", FlashMessages::SUCCESS);
            $this->presenter->redirect($this->redirect);
        } catch (BadCredentialsException $exception) {
            $form->addError("Neshoda přihlašovacího emailu a hesla");
        }
    }
}