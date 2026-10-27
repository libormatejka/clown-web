<?php declare(strict_types = 1);

namespace App;

use Nette\Bootstrap\Configurator;
use Nette\InvalidStateException;
use Nette\Utils\FileSystem;

class Bootstrap
{

	private const ASSETS_MANIFEST_PATH = __DIR__ . '/../config/manifest-assets.json';

	public static function boot(): Configurator
	{
		$configurator = new Configurator();
		$appDir = dirname(__DIR__);

		$debugMode = getenv('NETTE_DEBUG') === 'true';
		if (!defined('DEBUG_MODE')) {
			define('DEBUG_MODE', $debugMode);
		}

		$configurator->setDebugMode($debugMode);
		$configurator->enableTracy($appDir . '/log');
		$configurator->setTimeZone('Europe/Prague');
		$configurator->setTempDirectory($appDir . '/temp');

		$configurator->createRobotLoader()
			->addDirectory(__DIR__)
			->register();

		$configurator->addConfig($appDir . '/config/common.neon');
		if (file_exists($appDir . '/config/local.neon')) {
			$configurator->addConfig($appDir . '/config/local.neon');
		}

		return $configurator;
	}

	public static function loadAsset(string $asset): ?string
	{
		if (!file_exists(self::ASSETS_MANIFEST_PATH)) {
			return null;
		}

		$content = file_get_contents(self::ASSETS_MANIFEST_PATH);
		if ($content === false) {
			return null;
		}

		$manifest = json_decode($content, true);

		return is_array($manifest) ? ($manifest[$asset] ?? null) : null;
	}

	public static function getAsset(string $asset): string
	{
		$assetPath = self::loadAsset($asset);
		if ($assetPath !== null) {
			return $assetPath;
		}

		throw new InvalidStateException(sprintf(
			'Missing asset "%s" in "%s". Please compile assets using Webpack.',
			$asset,
			FileSystem::normalizePath(self::ASSETS_MANIFEST_PATH),
		));
	}

}
