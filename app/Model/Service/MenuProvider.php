<?php declare(strict_types = 1);

namespace App\Model\Service;

use Clown\Core\Entity\MenuItem;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Sestaví z entit MenuItem strom (pole), které očekávají core komponenty TopBar a Footer.
 */
final class MenuProvider
{

	public const MAIN_MENU_TITLE = 'Hlavní menu';

	public function __construct(private EntityManagerInterface $entityManager)
	{
	}

	/**
	 * @return array<int, array{id: int, slug: string, target: string|null, title: string, icon: string|null, description: string|null, hasSubmenu: bool, submenu: array<int, array<string, int|string|bool|null>>}>
	 */
	public function getMain(): array
	{
		/** @var array<int, MenuItem> $items */
		$items = $this->entityManager->createQueryBuilder()
			->select('i')
			->from(MenuItem::class, 'i')
			->join('i.menu', 'm')
			->where('m.title = :menu')
			->andWhere('i.status = :status')
			->setParameter('menu', self::MAIN_MENU_TITLE)
			->setParameter('status', 'publish')
			->orderBy('i.position', 'ASC')
			->getQuery()
			->getResult();

		$tree = [];
		foreach ($items as $item) {
			if ($item->getParentId() !== null) {
				continue;
			}

			$children = [];
			foreach ($items as $child) {
				if ($child->getParentId() === $item->getId()) {
					$children[] = $this->toArray($child, []);
				}
			}

			$tree[] = $this->toArray($item, $children);
		}

		return $tree;
	}

	/**
	 * @param array<int, array<string, int|string|bool|null>> $children
	 * @return array{id: int, slug: string, target: string|null, title: string, icon: string|null, description: string|null, hasSubmenu: bool, submenu: array<int, array<string, int|string|bool|null>>}
	 */
	private function toArray(MenuItem $item, array $children): array
	{
		return [
			'id' => $item->getId(),
			'slug' => $item->getSlug(),
			'target' => $item->getTarget(),
			'title' => $item->getTitle(),
			'icon' => $item->getIcon(),
			'description' => $item->getDescription(),
			'hasSubmenu' => $children !== [],
			'submenu' => $children,
		];
	}

}
