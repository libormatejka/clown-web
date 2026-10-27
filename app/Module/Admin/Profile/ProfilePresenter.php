<?php declare(strict_types = 1);

namespace App\Module\Admin\Profile;

use Clown\Core\Admin\AdminPresenter;
use Clown\Core\Settings\WebTitle;

/**
 * Core TopBarProfile odkazuje na Admin:Profile, ale core presenter neposkytuje —
 * tento minimální presenter jen zobrazí údaje přihlášeného uživatele.
 */
final class ProfilePresenter extends AdminPresenter
{

	public function __construct(private WebTitle $webTitle)
	{
		parent::__construct();
	}

	public function actionDefault(): void
	{
		$this->getMetaTags()->setTitle('Profil | ' . $this->webTitle);
		$this->template->headerBarsValues = [
			'title' => 'Profil',
			'buttons' => [],
		];
	}

}
