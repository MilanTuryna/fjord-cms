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
    const password = "password", email = "email", username = "username", first_name = "first_name", surname = "surname", permissions = "permissions", created = "created";

    private PermissionManager $permissionManager;
    private array $permissionMap = [];

    private bool $fullPermission = false;

    /**
     * Account constructor.
     * @param string $username
     * @param string $first_name
     * @param string $surname
     * @param string $email
     * @param string $password
     * @param string $permissions
     * @param string $created
     * @param int $id
     */
    public function __construct(public string $username, public string $first_name, public string $surname, public string $email, public string $password, public string $permissions, public string $created, public int $id) {
        $this->permissionManager = new AdminPermissions();
        $this->generatePermissionMap();
    }

    private function generatePermissionMap(): void
    {
        $allNodes = $this->permissionManager->getAllNodes();
        $adminPermissionsList = Utils::listToArray($this->permissions);
        $result = [];
        if(in_array(AdminPermissions::ADMIN_FULL, $adminPermissionsList)) $this->fullPermission = true;
        foreach ($allNodes as $node) $result[$node] = $this->fullPermission || Utils::checkPermission($adminPermissionsList, $node);
        $result[Utils::SPECIAL_WITHOUT_PERMISSION] = true;
        $this->permissionMap = $result;
        bdump($this->permissionMap);
    }

    /**
     * @return array
     */
    public function getPermissionMap(): array {
        return $this->permissionMap;
    }

    /**
     * @return PermissionManager
     * used in latte
     */
    public function getPermissionManager(): PermissionManager {
        return $this->permissionManager;
    }

    /**
     * @return bool
     */
    public function isFullPermission(): bool
    {
        return $this->fullPermission;
    }
}