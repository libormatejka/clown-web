<?php declare(strict_types = 1);

namespace App\Module\Admin\Post;

/**
 * Forwarding stub — Nette presenter mapping je granulární po modulu
 * ("Admin"), ne po jednotlivém presenteru, takže "Admin:Post" vždy
 * hledá třídu App\Module\Admin\Post\PostPresenter bez ohledu na to, že
 * reálná implementace žije v balíčku clown/post.
 */
final class PostPresenter extends \Clown\Post\Admin\PostPresenter
{

}
