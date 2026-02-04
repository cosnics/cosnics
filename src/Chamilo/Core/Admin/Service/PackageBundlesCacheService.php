<?php
namespace Chamilo\Core\Admin\Service;

use Chamilo\Core\Admin\Service\Finder\PackageBundlesGenerator;
use Chamilo\Libraries\Storage\Architecture\Interface\CacheDataPreLoaderInterface;
use Chamilo\Libraries\Storage\Architecture\Trait\SimpleCacheAdapterHandlerTrait;
use Chamilo\Libraries\Storage\Architecture\Trait\SimpleCacheDataPreLoaderTrait;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\Cache\Exception\CacheException;

/**
 * @package Chamilo\Core\Admin\Service
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class PackageBundlesCacheService implements CacheDataPreLoaderInterface
{
    use SimpleCacheAdapterHandlerTrait;
    use SimpleCacheDataPreLoaderTrait;

    protected PackageBundlesGenerator $packageBundlesGenerator;

    public function __construct(AdapterInterface $cacheAdapter, PackageBundlesGenerator $packageBundlesGenerator)
    {
        $this->cacheAdapter = $cacheAdapter;
        $this->packageBundlesGenerator = $packageBundlesGenerator;
    }

    /**
     * @return \Chamilo\Core\Admin\Storage\DataClass\Package[]
     */
    public function getDataForCache(): array
    {
        return $this->getPackageBundlesGenerator()->getPackages();
    }

    public function getPackageBundlesGenerator(): PackageBundlesGenerator
    {
        return $this->packageBundlesGenerator;
    }

    /**
     * @return \Chamilo\Core\Admin\Storage\DataClass\Package[]
     */
    public function getPackages(): array
    {
        try {
            return $this->loadCacheData();
        }
        catch (CacheException) {
            return $this->getDataForCache();
        }
    }
}