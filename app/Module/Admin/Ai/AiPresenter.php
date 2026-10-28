<?php declare(strict_types = 1);

namespace App\Module\Admin\Ai;

/**
 * Forwarding stub — Nette presenter mapping je granulární po modulu
 * ("Admin"), ne po jednotlivém presenteru, takže "Admin:Ai" vždy hledá
 * třídu App\Module\Admin\Ai\AiPresenter bez ohledu na to, že reálná
 * implementace žije v balíčku clown/ai.
 */
final class AiPresenter extends \Clown\Ai\Admin\Ai\AiPresenter
{

}
