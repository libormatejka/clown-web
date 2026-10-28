<?php declare(strict_types = 1);

namespace App\Module\Api\MediaLibrary;

/**
 * Forwarding stub — Nette presenter mapping je granulární po modulu
 * ("Api"), ne po jednotlivém presenteru, takže "Api:MediaLibrary" vždy
 * hledá třídu App\Module\Api\MediaLibrary\MediaLibraryPresenter bez ohledu na
 * to, že reálná implementace žije v balíčku clown/api.
 */
final class MediaLibraryPresenter extends \Clown\Api\MediaLibrary\MediaLibraryApiPresenter
{

}
