<?php declare(strict_types = 1);

namespace App\Module\Front\Page;

use App\Module\Front\FrontPresenter;
use Clown\Core\Entity\Page;
use Clown\Core\Settings\WebTitle;
use Doctrine\ORM\EntityManagerInterface;

final class PagePresenter extends FrontPresenter
{

	public function __construct(
		private EntityManagerInterface $entityManager,
		private WebTitle $webTitle,
	)
	{
		parent::__construct();
	}

	public function actionDetail(string $slug): void
	{
		$page = $this->entityManager->getRepository(Page::class)->findOneBy([
			'slug' => $slug,
			'status' => 'publish',
		]);

		if (!$page instanceof Page) {
			$this->error('Stránka nebyla nalezena.');
		}

		$this->template->page = $page;

		$metaTags = $this->getMetaTags();
		$metaTags->setTitle(($page->getMetaTitle() ?? $page->getTitle()) . ' | ' . $this->webTitle);
		$metaTags->setDescription($page->getMetaDescription() ?? $page->getPerex());
		$metaTags->setIndex($page->getMetaIndex());

		$this->setView('layout.' . ($page->getLayout() ?: 'one-column'));
	}

}
