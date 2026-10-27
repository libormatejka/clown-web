<?php declare(strict_types = 1);

namespace App\Module\Front\Homepage;

use App\Module\Front\FrontPresenter;
use Clown\Core\Settings\WebTitle;

final class HomepagePresenter extends FrontPresenter
{

	public function __construct(private WebTitle $webTitle)
	{
		parent::__construct();
	}

	public function actionDefault(): void
	{
		$this->getMetaTags()->setTitle((string) $this->webTitle);
		$this->template->webTitle = (string) $this->webTitle;
	}

}
