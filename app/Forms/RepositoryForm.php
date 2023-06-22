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
use Nette\Database\Table\ActiveRow;
use Nette\Forms\Controls\TextInput;
use Tracy\Debugger;

class RepositoryForm extends \App\Forms\Form
{
    protected IRepository $repository;

    /**
     * RepositoryForm constructor.
     * @param Presenter $presenter
     * @param Repository $repository
     */
    #[Pure] public function __construct(protected Presenter $presenter, IRepository $repository)
    {
        parent::__construct($this->presenter);

        $this->repository = $repository;
    }

    /**
     * @param Form $form
     * @param mixed $activeRow
     * @param string $submitCaption
     * @param array $exceptions For inputs that's not same as columns in database
     * @return Form
     */
    public static function createEditForm(Form $form, mixed $activeRow, string $submitCaption = "Aktualizovat změny", array $exceptions = []): Form
    {
        /**
         * @var $controls TextInput[]
         * Only for IDE hinting (non-tested)
         */
        $controls = $form->getControls();
        foreach ($controls as $input) {
            $inputName = $input->getName();
            if(!in_array($inputName, $exceptions) && $inputName !== "submit") {
                if(array_key_exists($inputName, $activeRow)) {
                    $form[$inputName]->setDefaultValue($activeRow[$inputName]);
                }
            }
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
                if($afterInsert) $afterInsert($updatedRow->{'id'});
                if($formRedirect) {
                    foreach ($formRedirect->args as $i => $v) { // replacing FormRedirect special argument constants as real value
                        if($v === FormRedirect::LAST_INSERT_ID) $formRedirect->args[$i] = is_int($updatedRow) ? $updatedRow : $updatedRow->{'id'};
                    }
                    $this->presenter->redirect($formRedirect->route, $formRedirect->args);
                }
            } else {
                $form->addError($message->error);
                $error = true;
            }
        } catch (Exception $exception) {
            // throw only redirect exception or if $throwExceptions will be true
            if($throwExceptions || !Debugger::$productionMode || $exception instanceof AbortException || $exception instanceof InvalidLinkException) throw $exception;
            $exceptionClass = get_class($exception);
            $form->addError($message->catchMessages[$exceptionClass] ?? $message->error);
            $error = true;
        }
        return !$error;
    }
}