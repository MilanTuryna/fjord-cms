<?php


namespace App\Presenters\Admin\Internal;


use App\Model\Admin\Permissions\Utils;
use App\Model\Database\Repository\Dynamic\EntityRepository;
use App\Model\Database\Repository\SMTP\ServerRepository;
use App\Model\Database\Repository\Template\Entity\Template;
use App\Model\Database\Repository\Template\TemplateRepository;
use App\Model\Security\Auth\AdminAuthenticator;
use App\Presenters\AdminBasePresenter;

class MainPresenter extends AdminBasePresenter
{
    public function __construct(AdminAuthenticator $adminAuthenticator, private ServerRepository $serverRepository, private TemplateRepository $templateRepository, string $permissionNode = Utils::SPECIAL_WITHOUT_PERMISSION)
    {
        parent::__construct($adminAuthenticator, $permissionNode);
    }

    public function renderHome() {
        $entityList = $this->template->entityList = $this->entityRepository->findAll()->fetchAll();
        $this->template->entityCount = count($entityList);
        $templates = $this->template->templates = $this->templateRepository->findAll()->fetchAll();
        $this->template->templateCount = count($templates);
        $template = $this->template->usedTemplate = $this->templateRepository->findByColumn(Template::used, true)->fetch();
        $this->template->usedTemplateAuthor = $template ? $template->related("author_id")->fetch() : null;
        $this->template->emailServers = $this->serverRepository->findAll()->fetchAll();
    }
}