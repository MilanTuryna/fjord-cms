<?php


namespace App\Forms\Template\Page\Variable;


use App\Forms\FormMessage;
use App\Forms\FormOption;
use App\Forms\FormRedirect;
use App\Model\Database\Repository\Template\Entity\PageVariable;
use App\Model\Database\Repository\Template\PageVariableRepository;
use JetBrains\PhpStorm\Pure;
use Nette\Application\AbortException;
use Nette\Application\UI\Form;
use Nette\Application\UI\InvalidLinkException;
use Nette\Application\UI\Presenter;

class EditPageVariableForm extends VariableForm
{
    /**
     * EditPageVariableForm constructor.
     * @param Presenter $presenter
     * @param PageVariableRepository $pageVariableRepository
     * @param int $pageId
     * @param FormRedirect $formRedirect
     * @param int $variableId
     * @param FormRedirect $deleteHref
     */
    #[Pure] public function __construct(Presenter $presenter, private PageVariableRepository $pageVariableRepository, private int $pageId, private FormRedirect $formRedirect, private int $variableId, private FormRedirect $deleteHref)
    {
        parent::__construct($presenter, $this->pageVariableRepository, $this->pageId, $this->formRedirect);
    }

    /**
     * @return Form
     */
    public function create(): Form
    {
        $form = parent::create();
        $variable = $this->pageVariableRepository->findById($this->variableId);
        $form["submit"]->setOption(FormOption::DELETE_LINK, $this->deleteHref);
        return $this::createEditForm($form, $variable);
    }

    /**
     * @throws InvalidLinkException
     * @throws AbortException
     */
    public function success(Form $form, PageVariable $data)
    {
        parent::success($form, $data);
        return $this->successTemplate($form, $data->iterable(), new FormMessage("Proměnná byla úspěšně aktualizovaná.", "Proměnná byla úspěšně aktualizovaná."), $this->formRedirect, $this->variableId);
    }
}