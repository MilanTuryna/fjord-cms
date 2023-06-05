<?php


namespace App\Forms;


use App\Model\Database\IRepository;
use App\Model\Database\Repository;
use App\Model\Database\Entity;
use Exception;
use JetBrains\PhpStorm\Pure;
use Nette\Application\AbortException;
use Nette\Application\UI\Form;
use Nette\Application\UI\InvalidLinkException;
use Nette\Application\UI\Presenter;
use Nette\Forms\Controls\TextInput;

class RepositoryForm extends \App\Forms\Form
{
    protected Repository $repository;

    /**
     * RepositoryForm constructor.
     * @param Presenter $presenter
     * @param Repository $repository
     */
    #[Pure] public function __construct(protected Presenter $presenter, Repository $repository)
    {
        parent::__construct($this->presenter);

        $this->repository = $repository;
    }

    /**
     * @param Form $form
     * @param object $activeRow
     * @param string $submitCaption
     * @param array $exceptions For inputs that's not same as columns in database
     * @return Form
     */
    public static function createEditForm(Form $form, object $activeRow, string $submitCaption = "Aktualizovat změny", array $exceptions = []): Form
    {
        /**
         * @var $controls TextInput[]
         * Only for IDE hinting (non-tested)
         */
        $controls = $form->getControls();
        foreach ($controls as $input) {
            $inputName = $input->getName();
            if($input->getOption(FormOption::OPTION_NOTE)) continue;
            if(!in_array($inputName, $exceptions) && $inputName !== "submit")
                if(property_exists($activeRow, $inputName)) $form[$inputName]->setDefaultValue($activeRow->{$inputName});
        }
        !isset($form['submit']) ? $form->addSubmit("submit", $submitCaption) : $form['submit']->setCaption($submitCaption);
        return $form;
    }

    /**
     * @param Form $form
     * @param iterable $entity
     * @param FormMessage $message
     * @param ?FormRedirect $formRedirect
     * @param int|null $rowId
     * @param array $dataExceptions
     * @param bool $throwExceptions
     * @param bool $deletePrivateVars
     * @param callable|null $afterInsert function($lastInsertId)
     * @return bool If process was OK
     * @throws AbortException
     * @throws InvalidLinkException
     */
    public function successTemplate(Form $form, iterable $entity, FormMessage $message, ?FormRedirect $formRedirect = null, ?int $rowId = null, array $dataExceptions = [], bool $throwExceptions = false, bool $deletePrivateVars = true, ?callable $afterInsert = null): bool
    {
        if(!$formRedirect) $formRedirect = new FormRedirect("this");
        $error = false;
        try {
            // delete "form private" vars
            if($deletePrivateVars) {
                foreach ($entity as $k => $i) if($k[0] === "_") unset($entity[$k]);
            }
            foreach ($dataExceptions as $dataException) unset($entity->{$dataException});
            $updatedRow = $rowId ? $this->repository->updateById($rowId, $entity) : $this->repository->insert($entity);
            if($updatedRow) {
                $this->presenter->flashMessage($message->success, FlashMessages::SUCCESS);
                foreach ($formRedirect->args as $i => $v) { // replacing FormRedirect special argument constants as real value
                    if($v === FormRedirect::LAST_INSERT_ID) $formRedirect->args[$i] = $updatedRow->{'id'};
                }
                if($afterInsert) $afterInsert($updatedRow->{'id'});
                $this->presenter->redirect($formRedirect->route, $formRedirect->args);
            } else {
                $form->addError($message->error);
                $error = true;
            }
        } catch (Exception $exception) {
            // throw only redirect exception or if $throwExceptions will be true
            if($throwExceptions || $exception instanceof AbortException || $exception instanceof InvalidLinkException) throw $exception;
            $exceptionClass = get_class($exception);
            $form->addError($message->catchMessages[$exceptionClass] ?? $message->error);
            $error = true;
        }
        return !$error;
    }
}