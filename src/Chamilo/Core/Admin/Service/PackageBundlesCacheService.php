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

    public function __construct(
        protected readonly AdapterInterface $cacheAdapter, protected PackageBundlesGenerator $packageBundlesGenerator
    )
    {
    }

    /**
     * @return \Chamilo\Core\Admin\Storage\DataClass\Package[]
     */
    public function getDataForCache(): array
    {
        return $this->packageBundlesGenerator->getPackages();
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