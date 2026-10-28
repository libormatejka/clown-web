<?php declare(strict_types = 1);

namespace App\Module\Admin\Block;

/**
 * Forwarding stub — Nette presenter mapping je granulární po modulu
 * ("Admin"), ne po jednotlivém presenteru, takže "Admin:Block" vždy
 * hledá třídu App\Module\Admin\Block\BlockPresenter bez ohledu na to,
 * že reálná implementace žije v balíčku clown/blocks.
 */
final class BlockPresenter extends \Clown\Blocks\Admin\BlockPresenter
{

}
