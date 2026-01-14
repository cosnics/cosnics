<?php
namespace Chamilo\Configuration\Service;

use Chamilo\Configuration\Service\Finder\PackageBundlesGenerator;
use Chamilo\Libraries\Cache\Interfaces\CacheDataPreLoaderInterface;
use Chamilo\Libraries\Cache\Traits\SimpleCacheAdapterHandlerTrait;
use Chamilo\Libraries\Cache\Traits\SimpleCacheDataPreLoaderTrait;
use Symfony\Component\Cache\Adapter\AdapterInterface;

/**
 * @package Chamilo\Configuration\Package\Service
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
     * @return \Chamilo\Configuration\Storage\DataClass\Package[]
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
     * @return \Chamilo\Configuration\Storage\DataClass\Package[]
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function getPackages(): array
    {
        return $this->loadCacheData();
    }
}