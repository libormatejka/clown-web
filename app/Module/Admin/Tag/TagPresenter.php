<?php declare(strict_types = 1);

namespace App\Module\Admin\Tag;

/**
 * Forwarding stub — Nette presenter mapping je granulární po modulu
 * ("Admin"), ne po jednotlivém presenteru, takže "Admin:Tag" vždy
 * hledá třídu App\Module\Admin\Tag\TagPresenter bez ohledu na to, že
 * reálná implementace žije v balíčku clown/taxonomy.
 */
final class TagPresenter extends \Clown\Taxonomy\Admin\TagPresenter
{

}
