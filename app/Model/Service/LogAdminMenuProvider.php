<?php declare(strict_types = 1);

namespace App\Model\Service;

use Clown\Core\AddonRegistry\AdminMenuItem;
use Clown\Core\AddonRegistry\IAdminMenuItemProvider;

final class LogAdminMenuProvider implements IAdminMenuItemProvider
{

	/**
	 * @return array<AdminMenuItem>
	 */
	public function getAdminMenuItems(): array
	{
		return [
			new AdminMenuItem(
				'Log',
				'Log aktivit',
				'<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6"><path fill-rule="evenodd" d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25ZM12.75 6a.75.75 0 0 0-1.5 0v6c0 .414.336.75.75.75h4.5a.75.75 0 0 0 0-1.5h-3.75V6Z" clip-rule="evenodd" /></svg>',
				'mr-3 h-5 w-5 shrink-0 text-gray-900',
				[],
				position: 800,
			),
		];
	}

}
