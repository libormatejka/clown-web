<?php declare(strict_types = 1);

namespace App\Model\Service;

use Nette\Utils\Json;

/**
 * Čte composer.lock a vrací nainstalované clown/* balíčky (core + addony) — pro admin přehled
 * "co je na tomhle webu zapojené", bez nutnosti volat composer show z konzole.
 */
final class PackageCatalogService
{

	public function __construct(private string $composerLockPath)
	{
	}

	/**
	 * @return array<int, array{name: string, version: string, reference: string, description: string}>
	 */
	public function getClownPackages(): array
	{
		if (!is_file($this->composerLockPath)) {
			return [];
		}

		$content = file_get_contents($this->composerLockPath);
		if ($content === false) {
			return [];
		}

		$lock = Json::decode($content, true);
		$packages = [];

		foreach ((array) ($lock['packages'] ?? []) as $package) {
			$name = (string) ($package['name'] ?? '');
			if (!str_starts_with($name, 'clown/')) {
				continue;
			}

			$packages[] = [
				'name' => $name,
				'version' => (string) ($package['version'] ?? ''),
				'reference' => substr((string) ($package['source']['reference'] ?? ($package['dist']['reference'] ?? '')), 0, 7),
				'description' => (string) ($package['description'] ?? ''),
			];
		}

		usort($packages, static fn (array $a, array $b): int => $a['name'] === 'clown/core' ? -1 : ($b['name'] === 'clown/core' ? 1 : $a['name'] <=> $b['name']));

		return $packages;
	}

}
