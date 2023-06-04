<?php

declare(strict_types=1);

namespace App\Router;

use Nette;
use Nette\Application\Routers\RouteList;


final class RouterFactory
{
	use Nette\StaticClass;

	public static function createRouter(): RouteList
	{
		$router = new RouteList;
		$router->withModule("Admin")
            ->addRoute('/admin', 'Overview:home')
            ->addRoute("/admin/login", "Auth:login")
            ->addRoute("/admin/logout", "Auth:logout")
            ->addRoute("/admin/overview", "Overview:home")

            // EAV manager
            ->addRoute("/admin/internal/eav/entity/list", "Internal:EAV:list")
            ->addRoute("/admin/internal/eav/entity/view/<id>", "Internal:EAV:view")
            ->addRoute("/admin/internal/eav/entity/new", "Internal:EAV:new")
            ->addRoute("/admin/internal/eav/entity/remove/<id>", "Internal:EAV:remove")

            ->addRoute("/admin/entity/<entityName>/list", "Dynamic:Entity:list")
            ->addRoute("/admin/entity/<entityName>/view/<rowUnique>", "Dynamic:Entity:view")
            ->addRoute("/admin/entity/<entityName>/remove/<rowUnique>", "Dynamic:Entity:remove")
            ->addRoute("/admin/entity/<entityName>/new", "Dynamic:Entity:new")

            ->addRoute("/admin/gallery/view/<galleryId>")
            ->addRoute("/admin/gallery/view/<galleryId>/image/view/<imageId>")
            ->addRoute("/admin/gallery/view/<galleryId>/image/remove/<imageId>")
            ->addRoute("/admin/gallery/remove/<galleryId>")

            ->addRoute("/admin/settings", "Settings:overview")
            ->addRoute("/admin/settings/history/<id>", "Settings:history")
        ;
		return $router;
	}
}
