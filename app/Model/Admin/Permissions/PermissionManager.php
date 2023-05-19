<?php


namespace App\Model\Admin\Permissions;


/**
 * Class PermissionManager
 * @package App\Model\Admin\Permissions
 */
abstract class PermissionManager
{
    public array $permissionSelectBox;

    /**
     * PermissionManager constructor.
     * @param array $permissionSelectBox ['node' => 'about permission']
     */
    public function __construct(array $permissionSelectBox = []) {
        $this->permissionSelectBox = $permissionSelectBox;
    }

    /**
     * @return array
     */
    public function getAllNodes(): array {
        return array_keys($this->permissionSelectBox);
    }
}