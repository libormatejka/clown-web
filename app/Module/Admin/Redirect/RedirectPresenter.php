<?php declare(strict_types = 1);

namespace App\Module\Admin\Redirect;

/**
 * Forwarding stub — Nette presenter mapping je granulární po modulu
 * ("Admin"), ne po jednotlivém presenteru, takže "Admin:Redirect" vždy
 * hledá třídu App\Module\Admin\Redirect\RedirectPresenter bez ohledu na
 * to, že reálná implementace žije v balíčku clown/redirect.
 */
final class RedirectPresenter extends \Clown\Redirect\Admin\RedirectPresenter
{

}
