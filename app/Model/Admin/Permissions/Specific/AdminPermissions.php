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

    const CREATE_PAGES = "page_create";
    const EDIT_PAGES = "page_edit";

    const CUSTOM_WIDGETS = "custom_widgets"; // create anonym widgets (in form without saving)
    const USE_WIDGETS = "use_widgets";

    const GALLERY_EDIT = "gallery_edit";
    CONST GALLERY_PHOTO = "gallery_add";

    const UPLOAD = "upload";

    #[Pure] public function __construct()
    {
        parent::__construct(self::selectBox());
    }

    public static function selectBox(): array {
        return [
            self::ADMIN_FULL => "Plná práva (superuser)",

            self::DEVELOPER_SETTINGS => "Správa vývojářského nastavení (šablony, widgety, web...)",

            self::DYNAMIC_ENTITY_ADMIN => "Tvorba vlastních entit (položek)",
            self::DYNAMIC_ENTITY_EDIT => "Správa a používání existujicích vlastních entit",

            self::CREATE_PAGES => "Vytváření nových stránek a záložek",
            self::EDIT_PAGES => "Správa již vytvořených stránek",

            self::CUSTOM_WIDGETS => "Tvorba vlastních widgetů",
            self::USE_WIDGETS => "Používání již vytvořených widgetů",

            self::GALLERY_EDIT => "Tvorba a správa galerií",
            self::GALLERY_PHOTO => "Správa obsahu jednotlivých galerií (fotky/videa)",

            self::UPLOAD => "Nahrávání/upload souborů"
        ];
    }
}