<?php declare(strict_types = 1);

namespace App\Module\Admin\Log;

use Clown\Core\Admin\AdminPresenter;
use Clown\Core\Admin\Component\DataGrid\DataGridTemplatePaths;
use Clown\Core\Admin\Component\DataGrid\ITailwindDataGridFactory;
use Clown\Core\Admin\Component\DataGrid\TailwindDataGrid;
use Clown\Core\Entity\Log;
use Clown\Core\Entity\LogType;
use Clown\Core\Settings\WebTitle;
use Contributte\Datagrid\DataSource\DoctrineDataSource;

/**
 * Přehled auditního logu (vytvoření, úpravy a mazání entit, přihlášení a odhlášení),
 * nejnovější záznamy nahoře.
 */
final class LogPresenter extends AdminPresenter
{

	public function __construct(
		private ITailwindDataGridFactory $tailwindDataGridFactory,
		private WebTitle $webTitle,
	)
	{
		parent::__construct();
	}

	public function actionDefault(): void
	{
		$this->getMetaTags()->setTitle('Log aktivit | ' . $this->webTitle);
		$this->template->headerBarsValues = [
			'title' => 'Log aktivit',
			'buttons' => [],
		];
		$this->getTemplate()->setFile(__DIR__ . '/templates/Log.default.latte');
	}

	protected function createComponentGrid(): TailwindDataGrid
	{
		$qb = $this->entityManager->createQueryBuilder()
			->select('l', 'u', 'lt')
			->from(Log::class, 'l')
			->leftJoin('l.user', 'u')
			->leftJoin('l.logType', 'lt');

		$dataSource = new DoctrineDataSource($qb, 'id');

		$grid = $this->tailwindDataGridFactory->create();
		$grid->setPrimaryKey('id');
		$grid->setDataSource($dataSource);
		$grid->setDefaultSort(['publishedAt' => 'DESC']);

		$filterTextTemplatePath = DataGridTemplatePaths::filterText();
		$filterSelectTemplatePath = DataGridTemplatePaths::filterSelect();

		$grid->addColumnDateTime('publishedAt', 'Datum')
			->setRenderer(function (Log $log): string {
				return $log->getPublishedAt()?->format('d.m.Y H:i:s') ?? '—';
			})
			->setSortable();

		$grid->addColumnText('userEmail', 'Uživatel', 'u.email')
			->setRenderer(fn (Log $log): string => $log->getUser()?->getEmail() ?? '—')
			->setSortable()
			->setFilterText()
				->setTemplate($filterTextTemplatePath);

		$logTypeOptions = ['' => 'Vše'];
		foreach ($this->entityManager->getRepository(LogType::class)->findAll() as $logType) {
			$logTypeOptions[$logType->getId()] = $logType->getText();
		}

		$grid->addColumnText('logType', 'Akce', 'lt.text')
			->setRenderer(fn (Log $log): string => $log->getLogType()->getText())
			->setSortable()
			->setFilterSelect($logTypeOptions, 'lt.id')
				->setTemplate($filterSelectTemplatePath);

		$grid->addColumnText('entity', 'Entita')
			->setRenderer(function (Log $log): string {
				return $log->getEntity() . ($log->getEntityId() !== null ? ' #' . $log->getEntityId() : '');
			})
			->setSortable()
			->setFilterText()
				->setTemplate($filterTextTemplatePath);

		$grid->addColumnText('ipAddress', 'IP adresa')
			->setRenderer(fn (Log $log): string => $log->getIpAddress() ?? '—')
			->setSortable()
			->setFilterText()
				->setTemplate($filterTextTemplatePath);

		$grid->addColumnText('userAgent', 'Prohlížeč')
			->setRenderer(function (Log $log): string {
				$userAgent = htmlspecialchars($log->getUserAgent() ?? '—');

				return sprintf('<span class="block max-w-xs truncate" title="%s">%s</span>', $userAgent, $userAgent);
			})
			->setTemplateEscaping(false);

		return $grid;
	}

}
