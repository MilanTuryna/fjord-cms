<?php


namespace App\Model\Admin\Permissions\Specific;


use App\Model\Admin\Permissions\PermissionManager;
use App\Model\Admin\Permissions\Utils;
use JetBrains\PhpStorm\Pure;

/**
 * Class AdminPermissions
 * @package App\Model\Admin\Permissions\Specific
 */
class AdminPermissions extends PermissionManager
{
    const ADMIN_FULL = Utils::FULL_PERM;

    const DEVELOPER_SETTINGS = "developer_settings"; // templates, widgets, name of website...

    const DYNAMIC_ENTITY_ADMIN = "dyn";
    const DYNAMIC_ENTITY_EDIT = "dynamic_entity_edit";

    const GALLERY_EDIT = "gallery_edit";
    CONST GALLERY_PHOTO = "gallery_add";

    const UPLOAD = "upload";

    #[Pure] public function __construct()
    {
        parent::__construct(self::selectBox());
    }

    /**
     * @return string[]
     */
    public static function selectBox(): array {
        return [
            self::ADMIN_FULL => "Plná práva (superuser)",

            self::DEVELOPER_SETTINGS => "Správa interní administrace (šablony, widgety, web...)",

            self::DYNAMIC_ENTITY_ADMIN => "Tvorba vlastních entit (položek)",
            self::DYNAMIC_ENTITY_EDIT => "Správa a používání existujicích vlastních entit",

            self::GALLERY_EDIT => "Tvorba a správa galerií",

            self::UPLOAD => "Nahrávání/upload souborů"
        ];
    }
}