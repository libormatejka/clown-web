<?php declare(strict_types = 1);

namespace App\Module\Admin\Packages;

use App\Model\Service\PackageCatalogService;
use Clown\Core\Admin\AdminPresenter;
use Clown\Core\Admin\Component\DataGrid\DataGridTemplatePaths;
use Clown\Core\Admin\Component\DataGrid\ITailwindDataGridFactory;
use Clown\Core\Admin\Component\DataGrid\TailwindDataGrid;
use Clown\Core\Settings\WebTitle;
use Contributte\Datagrid\DataSource\ArrayDataSource;

/**
 * Read-only přehled nainstalovaných clown/* balíčků (core + addony) z composer.lock —
 * ať se admin nemusí dívat do README nebo spouštět composer show v konzoli.
 */
final class PackagesPresenter extends AdminPresenter
{

	public function __construct(
		private PackageCatalogService $packageCatalogService,
		private ITailwindDataGridFactory $tailwindDataGridFactory,
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
		$this->getTemplate()->setFile(__DIR__ . '/templates/Packages.default.latte');
	}

	protected function createComponentGrid(): TailwindDataGrid
	{
		$dataSource = new ArrayDataSource($this->packageCatalogService->getClownPackages());

		$grid = $this->tailwindDataGridFactory->create();
		$grid->setPrimaryKey('name');
		$grid->setDataSource($dataSource);

		$filterTextTemplatePath = DataGridTemplatePaths::filterText();

		$grid->addColumnText('name', 'Balíček')
			->setSortable()
			->setFilterText()
				->setTemplate($filterTextTemplatePath);

		$grid->addColumnText('version', 'Verze')
			->setSortable();

		$grid->addColumnText('reference', 'Commit')
			->setRenderer(fn (array $package): string => sprintf('<code>%s</code>', htmlspecialchars($package['reference'])))
			->setTemplateEscaping(false);

		$grid->addColumnText('description', 'Popis')
			->setSortable()
			->setFilterText()
				->setTemplate($filterTextTemplatePath);

		return $grid;
	}

}
