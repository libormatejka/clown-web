<?php declare(strict_types = 1);

namespace App\Module\Admin\Category;

/**
 * Forwarding stub — Nette presenter mapping je granulární po modulu
 * ("Admin"), ne po jednotlivém presenteru, takže "Admin:Category" vždy
 * hledá třídu App\Module\Admin\Category\CategoryPresenter bez ohledu na
 * to, že reálná implementace žije v balíčku clown/taxonomy.
 */
final class CategoryPresenter extends \Clown\Taxonomy\Admin\CategoryPresenter
{

}
