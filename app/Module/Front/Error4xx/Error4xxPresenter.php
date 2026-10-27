<?php declare(strict_types = 1);

namespace App\Module\Front\Error4xx;

use App\Module\Front\FrontPresenter;
use Nette\Application\BadRequestException;
use Nette\Application\Request;

final class Error4xxPresenter extends FrontPresenter
{

	public function startup(): void
	{
		parent::startup();

		$request = $this->getRequest();
		if ($request === null || !$request->isMethod(Request::FORWARD)) {
			$this->error();
		}
	}

	public function renderDefault(BadRequestException $exception): void
	{
		$this->getHttpResponse()->setCode($exception->getHttpCode());
		$this->getMetaTags()->setTitle('Stránka nenalezena');
		$this->getMetaTags()->setIndex('noindex');
	}

}
