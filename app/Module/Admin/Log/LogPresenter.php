<?php declare(strict_types = 1);

namespace App\Module\Admin\Log;

use Clown\Core\Admin\AdminPresenter;
use Clown\Core\Entity\Log;
use Clown\Core\Settings\WebTitle;

/**
 * Přehled auditního logu (vytvoření, úpravy a mazání entit, přihlášení a odhlášení),
 * nejnovější záznamy nahoře.
 */
final class LogPresenter extends AdminPresenter
{

	private const LIMIT = 200;

	public function __construct(
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
		$this->template->logs = $this->entityManager?->getRepository(Log::class)
			->findBy([], ['publishedAt' => 'DESC'], self::LIMIT) ?? [];
		$this->getTemplate()->setFile(__DIR__ . '/templates/Log.default.latte');
	}

}
