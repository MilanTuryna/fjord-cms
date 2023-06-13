<?php


namespace App\Presenters\Admin\Internal;


use App\Model\Admin\Permissions\Utils;
use App\Model\Database\Repository\Dynamic\EntityRepository;
use App\Model\Security\Auth\AdminAuthenticator;
use App\Presenters\AdminBasePresenter;

class MainPresenter extends AdminBasePresenter
{
    public function __construct(AdminAuthenticator $adminAuthenticator, string $permissionNode = Utils::SPECIAL_WITHOUT_PERMISSION)
    {
        parent::__construct($adminAuthenticator, $permissionNode);
    }

    public function renderHome() {
        $entityList = $this->template->entityList = $this->entityRepository->findAll()->fetchAll();
        $this->template->entityCount = count($entityList);
    }
}