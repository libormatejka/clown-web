<?php declare(strict_types = 1);

namespace App\Model\Service;

use Clown\Core\AddonRegistry\AdminMenuItem;
use Clown\Core\AddonRegistry\AdminMenuSubItem;
use Clown\Core\AddonRegistry\IAdminMenuItemProvider;

final class AdminMenuProvider implements IAdminMenuItemProvider
{

	private const ICON_CLASS = 'mr-3 h-5 w-5 shrink-0 text-gray-900';

	/**
	 * @return array<AdminMenuItem>
	 */
	public function getAdminMenuItems(): array
	{
		return [
			new AdminMenuItem(
				'Homepage',
				'Dashboard',
				'<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M11.47 3.84a.75.75 0 0 1 1.06 0l8.69 8.69a.75.75 0 1 0 1.06-1.06l-8.69-8.69a2.25 2.25 0 0 0-3.18 0l-8.69 8.69a.75.75 0 0 0 1.06 1.06l8.69-8.69Z"/><path d="m12 5.43 8.16 8.16c.03.03.06.06.09.09v6.2c0 1.03-.84 1.87-1.88 1.87H15a.75.75 0 0 1-.75-.75v-4.5a.75.75 0 0 0-.75-.75h-3a.75.75 0 0 0-.75.75V21a.75.75 0 0 1-.75.75H5.63a1.88 1.88 0 0 1-1.88-1.88v-6.2l.09-.09L12 5.43Z"/></svg>',
				self::ICON_CLASS,
				[],
				[],
				10,
			),
			new AdminMenuItem(
				'Page',
				'Stránky',
				'<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M5.625 1.5c-1.04 0-1.875.84-1.875 1.875v17.25c0 1.03.84 1.875 1.875 1.875h12.75c1.04 0 1.875-.84 1.875-1.875V12.75A3.75 3.75 0 0 0 16.5 9h-1.875a1.875 1.875 0 0 1-1.875-1.875V5.25A3.75 3.75 0 0 0 9 1.5H5.625Z"/><path d="M12.971 1.816A5.23 5.23 0 0 1 14.25 5.25v1.875c0 .207.168.375.375.375H16.5a5.23 5.23 0 0 1 3.434 1.279 9.768 9.768 0 0 0-6.963-6.963Z"/></svg>',
				self::ICON_CLASS,
				[
					new AdminMenuSubItem('default', 'Seznam stránek', null, ['default', 'update']),
					new AdminMenuSubItem('create', 'Přidat stránku'),
				],
				[],
				20,
			),
		];
	}

}
