<?php declare(strict_types = 1);

namespace App\Module\Front\Preview;

use App\Module\Front\FrontPresenter;
use Clown\Core\Entity\Page;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Náhled stránky (i nepublikované) pro přihlášené administrátory.
 * Core admin PagePresenter na tento presenter odkazuje (:Front:Preview:Page), ale core ho nedodává.
 */
final class PreviewPresenter extends FrontPresenter
{

	public function __construct(private EntityManagerInterface $entityManager)
	{
		parent::__construct();
	}

	public function startup(): void
	{
		parent::startup();

		if (!$this->getUser()->isLoggedIn()) {
			$this->error('Náhled je dostupný jen přihlášeným uživatelům.', 403);
		}
	}

	public function actionPage(string $slug): void
	{
		$page = $this->entityManager->getRepository(Page::class)->findOneBy(['slug' => $slug]);
		if (!$page instanceof Page) {
			$this->error('Stránka nebyla nalezena.');
		}

		$this->template->page = $page;
		$this->getMetaTags()->setTitle('Náhled: ' . $page->getTitle());
		$this->getMetaTags()->setIndex('noindex, nofollow');
	}

}
