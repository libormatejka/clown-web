<?php declare(strict_types = 1);

use Nette\Application\Application;

require __DIR__ . '/../vendor/autoload.php';

$container = App\Bootstrap::boot()->createContainer();
$container->getByType(Application::class)->run();
