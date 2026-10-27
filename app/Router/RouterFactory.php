<?php declare(strict_types = 1);

namespace App\Router;

use Clown\Core\Routing\CoreRoutes;
use Nette\Application\Routers\RouteList;
use Nette\StaticClass;

final class RouterFactory
{

	use StaticClass;

	public static function createRouter(): RouteList
	{
		$router = new RouteList();
		$router->add(CoreRoutes::admin());
		$router->add(CoreRoutes::front());

		return $router;
	}

}
