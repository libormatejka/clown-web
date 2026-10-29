<?php declare(strict_types = 1);

namespace App\Module\Api\Gallery;

use Clown\Api\BaseApiPresenter;
use Clown\Gallery\Entity\Gallery;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Jediný spotřebitel je TinyMCE "Vložit galerii" shortcode tlačítko
 * (clown-core-assets/admin/js/TinymceEditor.js) — stejný vzor jako
 * App\Module\Api\Article\ArticlePresenter, viz tam.
 */
final class GalleryPresenter extends BaseApiPresenter
{

	private EntityManagerInterface $entityManager;

	public function __construct(EntityManagerInterface $entityManager)
	{
		parent::__construct();
		$this->entityManager = $entityManager;
	}

	public function actionGalleries(): void
	{
		$galleries = $this->entityManager->getRepository(Gallery::class)
			->findBy(['status' => 'publish'], ['title' => 'ASC']);

		$this->sendJson(array_map(
			static fn (Gallery $gallery): array => [
				'id' => $gallery->getId(),
				'title' => $gallery->getTitle(),
			],
			$galleries,
		));
	}

}
