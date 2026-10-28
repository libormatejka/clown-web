<?php declare(strict_types = 1);

namespace App\Module\Admin\Blog;

/**
 * Forwarding stub — Nette presenter mapping je granulární po modulu
 * ("Admin"), ne po jednotlivém presenteru, takže "Admin:Blog" vždy
 * hledá třídu App\Module\Admin\Blog\BlogPresenter bez ohledu na to, že
 * reálná implementace žije v balíčku clown/blog.
 */
final class BlogPresenter extends \Clown\Blog\Admin\BlogPresenter
{

}
