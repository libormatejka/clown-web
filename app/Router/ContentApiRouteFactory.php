<?php declare(strict_types = 1);

namespace App\Router;

use Clown\Core\AddonRegistry\IRouteFactory;
use Nette\Application\Routers\RouteList;
use Nette\Routing\Router;

/**
 * Routy pro site-specific per-content API presentery (viz
 * docs/CMS_CORE_MIGRATION_PLAN.md, B10 — clown/api sám o sobě obsah
 * content addonů neřeší), stejný vzor jako
 * Clown\Api\MediaLibrary\MediaLibraryRouteFactory.
 */
final class ContentApiRouteFactory implements IRouteFactory
{

	public function createRoute(): Router
	{
		$router = new RouteList('Api');
		$router->addRoute('api/v1/articles/', 'Article:articles');
		$router->addRoute('api/v1/posts/', 'Post:posts');
		$router->addRoute('api/v1/reviews/', 'Review:reviews');
		$router->addRoute('api/v1/galleries/', 'Gallery:galleries');

		return $router;
	}

}
