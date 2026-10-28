<?php declare(strict_types = 1);

namespace App\Module\Admin\Podcast;

/**
 * Forwarding stub — Nette presenter mapping je granulární po modulu
 * ("Admin"), ne po jednotlivém presenteru, takže "Admin:Podcast" vždy
 * hledá třídu App\Module\Admin\Podcast\PodcastPresenter bez ohledu na
 * to, že reálná implementace žije v balíčku clown/podcast.
 */
final class PodcastPresenter extends \Clown\Podcast\Admin\PodcastPresenter
{

}
