<?php


namespace App\Model\Database\Repository\Admin\Entity;


use App\Model\Admin\Permissions\PermissionManager;
use App\Model\Admin\Permissions\Specific\AdminPermissions;
use App\Model\Admin\Permissions\Utils;
use App\Model\Database\Entity;

/**
 * Class Account
 * @package App\Model\Database\Repository\Admin\Entity
 */
class Account extends Entity
{
    const password = "password", email = "email", username = "username";

    private PermissionManager $permissionManager;
    private array $permissionMap = [];

    /**
     * Account constructor.
     * @param string $username
     * @param string $email
     * @param string $password
     * @param string $permissions
     * @param int $id
     */
    public function __construct(public string $username, public string $email, public string $password, public string $permissions, public int $id) {
        $this->permissionManager = new AdminPermissions();
        $this->generatePermissionMap();
    }

    private function generatePermissionMap(): void
    {
        $allNodes = $this->permissionManager->getAllNodes();
        $adminPermissionsList = Utils::listToArray($this->permissions);
        $result = [];
        foreach ($allNodes as $node) $result[$node] = $this->isFullPermission() || Utils::checkPermission($adminPermissionsList, $node);
        $result[Utils::SPECIAL_WITHOUT_PERMISSION] = true;
        $this->permissionMap = $result;
    }

    /**
     * @return array
     */
    public function getPermissionMap(): array {
        return $this->permissionMap;
    }

    /**
     * @return bool
     */
    public function isFullPermission(): bool
    {
        return $this->permissions == "*";
    }
}