<?php declare(strict_types = 1);

namespace App\Model\Service;

use App\Bootstrap;
use Clown\Core\Front\Component\Assets\IAssetManifestResolver;

final class AssetManifestResolver implements IAssetManifestResolver
{

	public function resolve(string $asset): ?string
	{
		return Bootstrap::loadAsset($asset);
	}

}
