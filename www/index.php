<?php declare(strict_types = 1);

use Nette\Application\Application;

require __DIR__ . '/../vendor/autoload.php';

$container = App\Bootstrap::boot()->createContainer();
$application = $container->getByType(Application::class);

// Redirect: handle DB-managed 301/302 redirects before application routing
$container->getByType(Clown\Redirect\RedirectStartup::class)->register($application);

$application->run();
