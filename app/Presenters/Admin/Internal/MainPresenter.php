<?php


namespace App\Presenters\Admin\Internal;


use App\Model\Admin\Permissions\Specific\AdminPermissions;
use App\Model\Database\Repository\Dynamic\Entity\DynamicEntity;
use App\Model\Database\Repository\SMTP\ServerRepository;
use App\Model\Database\Repository\Template\TemplateRepository;
use App\Model\Security\Auth\AdminAuthenticator;
use App\Presenters\AdminBasePresenter;

class MainPresenter extends AdminBasePresenter
{
    public function __construct(AdminAuthenticator $adminAuthenticator, private ServerRepository $serverRepository, private TemplateRepository $templateRepository, string $permissionNode = AdminPermissions::DEVELOPER_SETTINGS)
    {
        parent::__construct($adminAuthenticator, $permissionNode);
    }

    public function renderHome() {
        $entityList = $this->template->generalEntities = $this->entityRepository->findByColumn(DynamicEntity::generated_by, "")->fetchAll();
        $this->template->entityCount = count($entityList);
        $templates = $this->template->templates = $this->templateRepository->findAll()->fetchAll();
        $this->template->templateCount = count($templates);
        $emailServers = $this->template->emailServers = $this->serverRepository->findAll()->fetchAll();
        $mailCounts = [];
        foreach ($emailServers as $emailServer) $mailCounts[$emailServer->id] = $emailServer->related("fjord_smtp_mails.id")->count("*");
        $this->template->mailCounts = $mailCounts;
    }
}