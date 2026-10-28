<?php declare(strict_types = 1);

namespace App\Module\Admin\ApiLog;

/**
 * Forwarding stub — Nette presenter mapping je granulární po modulu
 * ("Admin"), ne po jednotlivém presenteru, takže "Admin:ApiLog" vždy
 * hledá třídu App\Module\Admin\ApiLog\ApiLogPresenter bez ohledu na
 * to, že reálná implementace žije v balíčku clown/api.
 */
final class ApiLogPresenter extends \Clown\Api\Admin\ApiLogPresenter
{

}
