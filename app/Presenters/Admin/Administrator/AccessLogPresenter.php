<?php

namespace App\Presenters\Admin\Administrator;

use App\Model\Admin\Permissions\Specific\AdminPermissions;
use App\Model\Admin\Permissions\Utils;
use App\Model\Database\Repository;
use App\Model\Database\Repository\Admin\AccessLogRepository;
use App\Model\Database\Repository\Admin\Entity\AccessLog;
use App\Model\Security\Auth\AdminAuthenticator;
use App\Presenters\AdminBasePresenter;

/**
 *
 */
class AccessLogPresenter extends AdminBasePresenter
{
    /**
     * @param AdminAuthenticator $adminAuthenticator
     * @param AccessLogRepository $accessLogRepository
     * @param string $permissionNode
     */
    public function __construct(AdminAuthenticator $adminAuthenticator, private AccessLogRepository $accessLogRepository, string $permissionNode = AdminPermissions::ADMIN_FULL)
    {
        parent::__construct($adminAuthenticator, $permissionNode);
    }

    public function renderGlobal(int $page = 1): void {
        $accessLogs = $this->accessLogRepository->findAll()->order(AccessLog::created . " DESC");

        $lastPage = 0;
        $this->template->accessLogs = $accessLogs->page($page, 80, $lastPage);
        $this->template->administrators = Repository::generateMap($this->accountRepository->findAll()->fetchAll(), "id");

        $this->template->page = $page;
        $this->template->lastPage = $lastPage;
    }

    public function renderView(int $id, int $page = 1): void {
        $accessLogs = $this->accessLogRepository->findByColumn(AccessLog::admin_id, $id)->order(AccessLog::created . " DESC");

        $lastPage = 0;
        $this->template->accessLogs = $accessLogs->page($page, 80, $lastPage);
        $this->template->account = $this->accountRepository->findById($id);

        $this->template->page = $page;
        $this->template->lastPage = $lastPage;
    }
}