<?php


namespace App\Forms\Template\Page\Variable;


use App\Forms\Dynamic\Data\AttributeData;
use App\Forms\FormMessage;
use App\Forms\FormRedirect;
use App\Forms\RepositoryForm;
use App\Model\Database\IRepository;
use App\Model\Database\Repository\Template\Entity\PageVariable;
use App\Model\Database\Repository\Template\PageVariableRepository;
use JetBrains\PhpStorm\Pure;
use Nette\Application\AbortException;
use Nette\Application\UI\Form;
use Nette\Application\UI\InvalidLinkException;
use Nette\Application\UI\Presenter;
use Nette\DI\Container;

/**
 * Class VariableForm
 * @package App\Forms\Template\Page\Variable
 */
abstract class VariableForm extends RepositoryForm
{
    /**
     * VariableForm constructor.
     * @param Presenter $presenter
     * @param PageVariableRepository $pageVariableRepository
     * @param int $pageId
     * @param FormRedirect $formRedirect
     */
    #[Pure] public function __construct(Presenter $presenter, private PageVariableRepository $pageVariableRepository, private int $pageId, private FormRedirect $formRedirect)
    {
        parent::__construct($presenter,$this->pageVariableRepository);
        $this->presenter = $presenter;
    }

    /**
     * @return Form
     */
    public function create(): \Nette\Application\UI\Form
    {
        $form = parent::create();
        $form->addText(PageVariable::id_name, "Identifikátor proměnné")->setRequired(true);
        $form->addText(PageVariable::title, "Název proměnné")->setRequired(true);
        $form->addTextArea(PageVariable::description, "Popis proměnné");
        $form->addSelect(PageVariable::input_type, "Typ inputů", AttributeData::INPUT_TYPES);
        $form->addCheckbox("required", "Je proměnná povinná?")->setRequired(false);
        $form->addSubmit("submit", "Přidat proměnnou");
        return $form;
    }

    /**
     */
    public function success(Form $form, PageVariable $data) {
        $data->page_id = $this->pageId;
        $data->required = $data->required ?? false;
    }
}