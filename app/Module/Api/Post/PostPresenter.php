<?php declare(strict_types = 1);

namespace App\Module\Api\Post;

use Clown\Api\BaseApiPresenter;
use Clown\Post\Entity\Post;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Jediný spotřebitel je TinyMCE "Single Novinka" shortcode tlačítko
 * (clown-core-assets/admin/js/TinymceEditor.js) — stejný vzor jako
 * App\Module\Api\Article\ArticlePresenter, viz tam.
 */
final class PostPresenter extends BaseApiPresenter
{

	private EntityManagerInterface $entityManager;

	public function __construct(EntityManagerInterface $entityManager)
	{
		parent::__construct();
		$this->entityManager = $entityManager;
	}

	public function actionPosts(): void
	{
		$posts = $this->entityManager->getRepository(Post::class)
			->findBy(['status' => 'publish'], ['title' => 'ASC']);

		$this->sendJson(array_map(
			static fn (Post $post): array => [
				'id' => $post->getId(),
				'title' => $post->getTitle(),
			],
			$posts,
		));
	}

}
