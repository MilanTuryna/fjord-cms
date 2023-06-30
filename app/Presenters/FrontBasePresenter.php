<?php


namespace App\Presenters;

use App\Model\Database\Repository\Template\Entity\Template;
use App\Model\Database\Repository\Template\TemplateRepository;
use Nette\Application\UI\Presenter;
use Nette\Database\Table\ActiveRow;

/**
 * Class FrontBasePresenter
 * @package App\Presenters
 */
class FrontBasePresenter extends Presenter
{
    protected ActiveRow|Template|null $usedTemplate;

    /**
     * FrontBasePresenter constructor.
     * @param TemplateRepository $templateRepository
     */
    public function __construct(private TemplateRepository $templateRepository)
    {
        parent::__construct();

        $this->usedTemplate = $this->templateRepository->findByColumn(Template::used, 1)->fetch();
    }

    public function startup()
    {
        parent::startup();
        if(!$this->usedTemplate) {
            die("Na tomto webu právě probíhá údržba. Vyčkejte než skončí, případně kontaktujte administrátora webu.");
        }
    }
}