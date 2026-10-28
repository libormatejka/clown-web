<?php declare(strict_types = 1);

namespace App\Module\Admin\Gallery;

/**
 * Forwarding stub — Nette presenter mapping je granulární po modulu
 * ("Admin"), ne po jednotlivém presenteru, takže "Admin:Gallery" vždy
 * hledá třídu App\Module\Admin\Gallery\GalleryPresenter bez ohledu na
 * to, že reálná implementace žije v balíčku clown/gallery.
 */
final class GalleryPresenter extends \Clown\Gallery\Admin\GalleryPresenter
{

}
