<?php

declare(strict_types=1);

namespace App\Router;

use Nette;
use Nette\Application\Routers\RouteList;

/**
 * Class RouterFactory
 * @package App\Router
 */
final class RouterFactory
{
	use Nette\StaticClass;

	public static function createRouter(): RouteList
	{
		$router = new RouteList;
		$router->withModule("Admin")
            ->addRoute('/admin', 'Overview:home')
            ->addRoute("/admin/login", "Auth:login")
            ->addRoute("/admin/logout", "Overview:logout")
            ->addRoute("/admin/overview", "Overview:home")

            ->addRoute("/admin/internal", "Internal:Main:home")

            // EAV manager
            ->addRoute("/admin/internal/eav/entity/list", "Internal:EAV:list")
            ->addRoute("/admin/internal/eav/entity/view/<id>", "Internal:EAV:view")
            ->addRoute("/admin/internal/eav/entity/new", "Internal:EAV:new")
            ->addRoute("/admin/internal/eav/entity/remove/<id>", "Internal:EAV:remove")

            ->addRoute("/admin/entity/<entityName>/list", "Dynamic:Entity:list")
            ->addRoute("/admin/entity/<entityName>/view/<rowUnique>", "Dynamic:Entity:view")
            ->addRoute("/admin/entity/<entityName>/remove/<rowUnique>", "Dynamic:Entity:remove")
            ->addRoute("/admin/entity/<entityName>/new", "Dynamic:Entity:new")

            ->addRoute("/admin/gallery", "Gallery:Main:overview")
            ->addRoute("/admin/gallery/view/<galleryId>", "Gallery:Main:view")
            ->addRoute("/admin/gallery/view/<galleryId>/image/view/<imageId>", "Gallery:Main:viewImage")
            ->addRoute("/admin/gallery/view/<galleryId>/image/remove/<imageId>", "Gallery:Main:removeImage")
            ->addRoute("/admin/gallery/remove/<galleryId>", "Gallery:Main:remove")

            ->addRoute("/admin/account/list", "Administrator:Account:list")
            ->addRoute("/admin/account/new", "Administrator:Account:new")
            ->addRoute("/admin/account/view[/<id>]", "Administrator:Account:new")
            ->addRoute("/admin/account/remove[/<id>]", "Administrator:Account:remove")

            ->addRoute("/admin/settings", "Settings:overview")
            ->addRoute("/admin/settings/history/<id>", "Settings:history")
        ;
		return $router;
	}
}
