<?php

declare(strict_types=1);

namespace App\Router;

use App\Model\Database\Repository\Template\Entity\Page;
use App\Model\Database\Repository\Template\Entity\Template;
use App\Model\Database\Repository\Template\PageRepository;
use App\Model\Database\Repository\Template\TemplateRepository;
use App\Presenters\Admin\Internal\TemplatePresenter;
use Latte\Compiler\Nodes\Php\Expression\FilterCallNode;
use Nette;
use Nette\Application\Routers\RouteList;
use Nette\Routing\Route;

/**
 * Class RouterFactory
 * @package App\Router
 */
final class RouterFactory
{
	use Nette\StaticClass;

	public function __construct(private TemplateRepository $templateRepository, private PageRepository $pageRepository) {
    }

	public function createRouter(): RouteList
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

            ->addRoute("/admin/internal/eav/schema", "Internal:EAV:entitySchema")

            ->addRoute("/admin/internal/template/list", "Internal:Template:list")
            ->addRoute("/admin/internal/template/install", "Internal:Template:install")
            ->addRoute("/admin/internal/template/schema-generator", "Internal:Template:generateSchema") // schema
            // todo: history backups
            ->addRoute("/admin/internal/template/view/<id>", "Internal:Template:view")
            ->addRoute("/admin/internal/template/remove/<id>", "Internal:Template:remove")
            ->addRoute("/admin/internal/template/enable/<id>?value=<value>", "Internal:Template:enable")
            ->addRoute("/admin/internal/template/view/<templateId>/viewPage/<pageId>", "Internal:Template:viewPage")
            ->addRoute("/admin/internal/template/view/<templateId>/viewPage/<pageId>/variables/new", "Internal:Template:newVariable")
            ->addRoute("/admin/internal/template/view/<templateId>/viewPage/<pageId>/variables/view/<variableId>", "Internal:Template:viewVariable")
            ->addRoute("/admin/internal/template/view/<templateId>/viewPage/<pageId>/variables/remove/<variableId>", "Internal:Template:removeVariable")

            ->addRoute("/admin/internal/smtp/new", "Internal:SMTP:new")
            ->addRoute("/admin/internal/smtp/view/<id>", "Internal:SMTP:view")
            ->addRoute("/admin/internal/smtp/view/<server_id>/mail/<id>", "Internal:SMTP:viewMail")
            ->addRoute("/admin/internal/smtp/remove/<id>", "Internal:SMTP:remove")

            ->addRoute("/admin/entity/<entityName>/list", "Dynamic:Entity:list")
            ->addRoute("/admin/entity/<entityName>/view/<rowUnique>", "Dynamic:Entity:view")
            ->addRoute("/admin/entity/<entityName>/remove/<rowUnique>", "Dynamic:Entity:remove")
            ->addRoute("/admin/entity/<entityName>/new", "Dynamic:Entity:new")

            ->addRoute("/admin/gallery", "Gallery:Main:overview")
            ->addRoute("/admin/gallery/new", "Gallery:Main:new")
            ->addRoute("/admin/gallery/view/<galleryId>", "Gallery:Main:view")
            ->addRoute("/admin/gallery/view/<galleryId>/image/view/<imageId>", "Gallery:Main:viewImage")
            ->addRoute("/admin/gallery/view/<galleryId>/image/remove/<imageId>", "Gallery:Main:removeImage")
            ->addRoute("/admin/gallery/remove/<galleryId>", "Gallery:Main:remove")
            ->addRoute("/admin/gallery/remove-images/<galleryId>", "Gallery:Main:removeImages")

            ->addRoute("/admin/upload/list", "Administrator:Upload:list")
            ->addRoute("/admin/upload/remove-file/<fileName>", "Administrator:Upload:remove")

            ->addRoute("/admin/account/list", "Administrator:Account:list")
            ->addRoute("/admin/account/new", "Administrator:Account:new")
            ->addRoute("/admin/account/view/<id>", "Administrator:Account:view")
            ->addRoute("/admin/account/remove/<id>", "Administrator:Account:remove")
            ->addRoute("/admin/account/view/<id>/access-log[/<page>]", "Administrator:AccessLog:view")

            ->addRoute("/admin/access-log/global[/<page>]", "Administrator:AccessLog:global")

            ->addRoute("/admin/settings", "Settings:overview")
            ->addRoute("/admin/settings/history/<id>", "Settings:history")
        ;
        $router->withModule("Front")->addRoute("/dependencies/js", "Generator:loaderJavascript");
        $router->withModule("Front")->addRoute("/dependencies/css", "Generator:loaderCss");
        $router->withModule("Front")->addRoute("/_error_404", "Generator:404");
        $template = $this->templateRepository->findAll()->where(Template::used . " = ?", 1)->fetch();
        /**
         * @var $pages Page[]
         */
        $pages = $this->pageRepository->findByColumn(Page::template_id, $template->id)->fetchAll();
        foreach ($pages as $page) {
            $router->withModule("Front")->addRoute($page->route, [
                "presenter" => "Generator",
                "action" => "url",
                "pageRoute" => $page->route,
                "pageId" => $page->id,
            ]);
        }
		return $router;
	}
}
