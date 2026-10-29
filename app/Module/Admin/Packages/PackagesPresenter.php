<?php declare(strict_types = 1);

namespace App\Module\Admin\Packages;

use App\Model\Service\PackageCatalogService;
use Clown\Core\Admin\AdminPresenter;
use Clown\Core\Settings\WebTitle;

/**
 * Read-only přehled nainstalovaných clown/* balíčků (core + addony) z composer.lock —
 * ať se admin nemusí dívat do README nebo spouštět composer show v konzoli.
 */
final class PackagesPresenter extends AdminPresenter
{

	public function __construct(
		private PackageCatalogService $packageCatalogService,
		private WebTitle $webTitle,
	)
	{
		parent::__construct();
	}

	public function actionDefault(): void
	{
		$this->getMetaTags()->setTitle('Balíčky | ' . $this->webTitle);
		$this->template->headerBarsValues = [
			'title' => 'Nainstalované balíčky',
			'buttons' => [],
		];
		$this->template->packages = $this->packageCatalogService->getClownPackages();
		$this->getTemplate()->setFile(__DIR__ . '/templates/Packages.default.latte');
	}

}
