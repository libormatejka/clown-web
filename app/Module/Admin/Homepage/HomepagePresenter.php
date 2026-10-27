<?php declare(strict_types = 1);

namespace App\Module\Admin\Homepage;

use Clown\Core\Admin\AdminPresenter;
use Clown\Core\Entity\Page;
use Clown\Core\Settings\WebTitle;

final class HomepagePresenter extends AdminPresenter
{

	public function __construct(private WebTitle $webTitle)
	{
		parent::__construct();
	}

	public function actionDefault(): void
	{
		$this->getMetaTags()->setTitle('Dashboard | ' . $this->webTitle);

		$pageRepository = $this->entityManager?->getRepository(Page::class);
		$this->template->pagesCount = $pageRepository?->count([]) ?? 0;
		$this->template->headerBarsValues = [
			'title' => 'Dashboard',
			'buttons' => [],
		];
	}

}
