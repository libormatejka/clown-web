<?php declare(strict_types = 1);

namespace App\Module\Api\Article;

use Clown\Api\BaseApiPresenter;
use Clown\Articles\Entity\Article;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Jediný spotřebitel je TinyMCE "Single Článek" shortcode tlačítko
 * (clown-core-assets/admin/js/TinymceEditor.js), které čte jen id/title —
 * žádná GraphQL/generátorová vrstva, web má přímý Doctrine dotaz, stejně
 * jako Clown\Api\MediaLibrary\MediaLibraryApiPresenter (viz
 * docs/CMS_CORE_MIGRATION_PLAN.md, B10 — per-content API presentery
 * zůstávají v site repu, clown/api o content addonech nic neví).
 */
final class ArticlePresenter extends BaseApiPresenter
{

	private EntityManagerInterface $entityManager;

	public function __construct(EntityManagerInterface $entityManager)
	{
		parent::__construct();
		$this->entityManager = $entityManager;
	}

	public function actionArticles(): void
	{
		$articles = $this->entityManager->getRepository(Article::class)
			->findBy(['status' => 'publish'], ['title' => 'ASC']);

		$this->sendJson(array_map(
			static fn (Article $article): array => [
				'id' => $article->getId(),
				'title' => $article->getTitle(),
			],
			$articles,
		));
	}

}
