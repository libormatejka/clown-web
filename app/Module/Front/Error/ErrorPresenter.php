<?php declare(strict_types = 1);

namespace App\Module\Front\Error;

use Nette\Application\BadRequestException;
use Nette\Application\Helpers;
use Nette\Application\IPresenter;
use Nette\Application\Request;
use Nette\Application\Response;
use Nette\Application\Responses\CallbackResponse;
use Nette\Application\Responses\ForwardResponse;
use Nette\Http\IRequest;
use Nette\Http\IResponse;
use Tracy\ILogger;

final class ErrorPresenter implements IPresenter
{

	public function __construct(private ILogger $logger)
	{
	}

	public function run(Request $request): Response
	{
		$exception = $request->getParameter('exception');

		if ($exception instanceof BadRequestException) {
			[$module, , $sep] = Helpers::splitName($request->getPresenterName());

			return new ForwardResponse($request->setPresenterName($module . $sep . 'Error4xx'));
		}

		$this->logger->log($exception, ILogger::EXCEPTION);

		return new CallbackResponse(function (IRequest $httpRequest, IResponse $httpResponse): void {
			if (preg_match('#^text/html(?:;|$)#', (string) $httpResponse->getHeader('Content-Type'))) {
				echo '<!doctype html><meta charset="utf-8"><title>Chyba serveru</title><h1>Chyba serveru</h1><p>Něco se pokazilo. Zkuste to prosím později.</p>';
			}
		});
	}

}
