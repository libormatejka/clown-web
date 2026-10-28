<?php declare(strict_types = 1);

namespace App\Module\Admin\Article;

/**
 * Forwarding stub — Nette presenter mapping je granulární po modulu
 * ("Admin"), ne po jednotlivém presenteru, takže "Admin:Article" vždy
 * hledá třídu App\Module\Admin\Article\ArticlePresenter bez ohledu na
 * to, že reálná implementace žije v balíčku clown/articles.
 */
final class ArticlePresenter extends \Clown\Articles\Admin\ArticlePresenter
{

}
