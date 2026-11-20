<?php declare(strict_types = 1);

namespace App\Module\Admin\Homepage;

use Clown\Core\Admin\AdminPresenter;
use Clown\Core\Entity\Log;
use Clown\Core\Entity\MediaLibrary;
use Clown\Core\Entity\Page;
use Clown\Core\Entity\User;
use Clown\Core\Settings\MediaLibraryDir;
use Clown\Core\Settings\WebTitle;

/**
 * Přebírá výchozí core dashboard (Clown\Core\Admin\Homepage\HomepagePresenter)
 * vlastní verzí podle dashboardu na collectorboy.cz/admin, adaptovanou na
 * core-only obsah (jen Page/MediaLibrary/User/Log — bez postů, článků, recenzí,
 * štítků a kategorií, které core nemá).
 */
final class HomepagePresenter extends AdminPresenter
{

	public function __construct(
		private WebTitle $webTitle,
		private MediaLibraryDir $mediaLibraryDir,
	)
	{
		parent::__construct();
	}

	public function actionDefault(): void
	{
		$this->getMetaTags()->setTitle('Dashboard | ' . $this->webTitle);

		$identity = $this->getUser()->getIdentity();
		$this->template->firstname = $identity?->firstname ?? null;
		$this->template->datetime = new \DateTime();

		$this->template->pagesCount = $this->entityManager?->getRepository(Page::class)->count([]) ?? 0;
		$this->template->mediaCount = $this->entityManager?->getRepository(MediaLibrary::class)->count([]) ?? 0;
		$this->template->usersCount = $this->entityManager?->getRepository(User::class)->count([]) ?? 0;

		$this->template->pages = $this->entityManager?->getRepository(Page::class)
			->findBy([], ['publishedAt' => 'DESC'], 30) ?? [];

		$this->template->files = $this->entityManager?->getRepository(MediaLibrary::class)
			->findBy([], ['publishedAt' => 'DESC'], 30) ?? [];

		$this->template->users = $this->entityManager?->getRepository(User::class)
			->findBy([], ['createdAt' => 'DESC'], 30) ?? [];

		$this->template->logs = $this->entityManager?->getRepository(Log::class)
			->findBy([], ['publishedAt' => 'DESC'], 10) ?? [];
	}

	public function getThumbnailSrc(?MediaLibrary $media): ?string
	{
		if ($media === null) {
			return null;
		}

		return $this->mediaLibraryDir . '/' . $media->getPath() . $media->getFilename();
	}

}
