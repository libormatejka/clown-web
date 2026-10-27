<?php declare(strict_types = 1);

namespace App\Module\Front;

use App\Model\Service\MenuProvider;
use Clown\Core\Front\Component\Assets\Assets;
use Clown\Core\Front\Component\Assets\IAssetsFactory;
use Clown\Core\Front\Component\Footer\Footer;
use Clown\Core\Front\Component\Footer\IFooterFactory;
use Clown\Core\Front\Component\MetaTags\IMetaTagsFactory;
use Clown\Core\Front\Component\MetaTags\MetaTags;
use Clown\Core\Front\Component\TopBar\ITopBarFactory;
use Clown\Core\Front\Component\TopBar\TopBar;
use Nette\Application\UI\Presenter;

abstract class FrontPresenter extends Presenter
{

	private ITopBarFactory $topBarFactory;
	private IFooterFactory $footerFactory;
	private IMetaTagsFactory $metaTagsFactory;
	private IAssetsFactory $assetsFactory;
	private MenuProvider $menuProvider;

	public function injectChrome(
		ITopBarFactory $topBarFactory,
		IFooterFactory $footerFactory,
		IMetaTagsFactory $metaTagsFactory,
		IAssetsFactory $assetsFactory,
		MenuProvider $menuProvider,
	): void
	{
		$this->topBarFactory = $topBarFactory;
		$this->footerFactory = $footerFactory;
		$this->metaTagsFactory = $metaTagsFactory;
		$this->assetsFactory = $assetsFactory;
		$this->menuProvider = $menuProvider;
	}

	public function getMetaTags(): MetaTags
	{
		$metaTags = $this->getComponent('metaTags');
		assert($metaTags instanceof MetaTags);

		return $metaTags;
	}

	protected function createComponentTopBar(): TopBar
	{
		return $this->topBarFactory->create();
	}

	protected function createComponentFooter(): Footer
	{
		return $this->footerFactory->create();
	}

	protected function createComponentMetaTags(): MetaTags
	{
		return $this->metaTagsFactory->create();
	}

	protected function createComponentAssets(): Assets
	{
		return $this->assetsFactory->create();
	}

	protected function beforeRender(): void
	{
		$menuData = $this->menuProvider->getMain();
		$this->template->topBarMenuData = $menuData;
		$this->template->footerMenuData = $menuData;
	}

}
