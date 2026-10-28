<?php declare(strict_types = 1);

namespace App\Router;

use Clown\Core\AddonRegistry\RouteRegistry;
use Clown\Core\Routing\CoreRoutes;
use Nette\Application\Routers\RouteList;
use Nette\StaticClass;

final class RouterFactory
{

	use StaticClass;

	/**
	 * Addon routy (clown.route) musí přijít před CoreRoutes::front(), protože
	 * ta končí catch-all routou <slug>/ — stejné pravidlo jako pro
	 * site-specific routy webu.
	 */
	public static function createRouter(RouteRegistry $routeRegistry): RouteList
	{
		$router = new RouteList();
		$router->add(CoreRoutes::admin());
		$router->add($routeRegistry->createRouteList());
		$router->add(CoreRoutes::front());

		return $router;
	}

}
