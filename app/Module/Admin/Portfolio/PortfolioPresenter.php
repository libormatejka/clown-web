<?php declare(strict_types = 1);

namespace App\Module\Admin\Portfolio;

/**
 * Forwarding stub — Nette presenter mapping je granulární po modulu
 * ("Admin"), ne po jednotlivém presenteru, takže "Admin:Portfolio"
 * vždy hledá třídu App\Module\Admin\Portfolio\PortfolioPresenter bez
 * ohledu na to, že reálná implementace žije v balíčku clown/portfolio.
 */
final class PortfolioPresenter extends \Clown\Portfolio\Admin\PortfolioPresenter
{

}
