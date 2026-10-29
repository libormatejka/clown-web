<?php declare(strict_types = 1);

namespace App\Module\Api\Review;

use Clown\Api\BaseApiPresenter;
use Clown\Reviews\Entity\Review;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Jediný spotřebitel je TinyMCE "Single Recenze" shortcode tlačítko
 * (clown-core-assets/admin/js/TinymceEditor.js) — stejný vzor jako
 * App\Module\Api\Article\ArticlePresenter, viz tam.
 */
final class ReviewPresenter extends BaseApiPresenter
{

	private EntityManagerInterface $entityManager;

	public function __construct(EntityManagerInterface $entityManager)
	{
		parent::__construct();
		$this->entityManager = $entityManager;
	}

	public function actionReviews(): void
	{
		$reviews = $this->entityManager->getRepository(Review::class)
			->findBy(['status' => 'publish'], ['title' => 'ASC']);

		$this->sendJson(array_map(
			static fn (Review $review): array => [
				'id' => $review->getId(),
				'title' => $review->getTitle(),
			],
			$reviews,
		));
	}

}
