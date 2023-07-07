<?php


namespace App\Forms\Template\Page\Variable;


use App\Forms\FormMessage;
use App\Forms\FormRedirect;
use App\Model\Database\Repository\Template\Entity\PageVariable;
use App\Model\Database\Repository\Template\PageVariableRepository;
use JetBrains\PhpStorm\Pure;
use Nette\Application\AbortException;
use Nette\Application\UI\Form;
use Nette\Application\UI\InvalidLinkException;
use Nette\Application\UI\Presenter;

/**
 * Class CreatePageVariableForm
 * @package App\Forms\Template\Page\Variable
 */
class CreatePageVariableForm extends VariableForm
{
    /**
     * EditPageVariableForm constructor.
     * @param Presenter $presenter
     * @param PageVariableRepository $pageVariableRepository
     * @param int $pageId
     * @param FormRedirect $formRedirect
     */
    #[Pure] public function __construct(Presenter $presenter, private PageVariableRepository $pageVariableRepository, private int $pageId, private FormRedirect $formRedirect)
    {
        parent::__construct($presenter, $this->pageVariableRepository, $this->pageId, $this->formRedirect);
    }

    /**
     * @throws InvalidLinkException
     * @throws AbortException
     */
    public function success(Form $form, PageVariable $data)
    {
        parent::success($form, $data);
        return $this->successTemplate($form, $data->iterable(), new FormMessage("Proměnná byla úspěšně vytvořena..", "Proměnná byla úspěšně vytvořena."), $this->formRedirect);
    }
}