<?php declare(strict_types = 1);

namespace App\Router;

use Nette\Application\Routers\RouteList;
use Nette\StaticClass;

final class RouterFactory
{

	use StaticClass;

	public static function createRouter(): RouteList
	{
		$adminRouter = new RouteList('Admin');
		$adminRouter->addRoute('admin/<presenter>/<action>[/<id>]', 'Homepage:default');

		$frontRouter = new RouteList('Front');
		$frontRouter->addRoute('', 'Homepage:default');
		$frontRouter->addRoute('preview/stranka/<slug>/', 'Preview:Page');
		$frontRouter->addRoute('<slug>/', 'Page:detail');

		$router = new RouteList();
		$router->add($adminRouter);
		$router->add($frontRouter);

		return $router;
	}

}
