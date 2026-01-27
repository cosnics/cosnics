<?php
namespace Chamilo\Core\Admin\Service;

use Chamilo\Core\Admin\Service\Finder\InternationalizationBundlesGenerator;
use Chamilo\Libraries\Cache\Interfaces\CacheDataPreLoaderInterface;
use Chamilo\Libraries\Cache\Traits\SimpleCacheAdapterHandlerTrait;
use Chamilo\Libraries\Cache\Traits\SimpleCacheDataPreLoaderTrait;
use Symfony\Component\Cache\Adapter\AdapterInterface;

/**
 * @package Chamilo\Core\Admin\Service
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class InternationalizationBundlesCacheService implements CacheDataPreLoaderInterface
{
    use SimpleCacheAdapterHandlerTrait;
    use SimpleCacheDataPreLoaderTrait;

    protected InternationalizationBundlesGenerator $internationalizationBundlesGenerator;

    public function __construct(
        InternationalizationBundlesGenerator $internationalizationBundlesGenerator, AdapterInterface $cacheAdapter
    )
    {
        $this->internationalizationBundlesGenerator = $internationalizationBundlesGenerator;
        $this->cacheAdapter = $cacheAdapter;
    }

    /**
     * @return string[]
     */
    protected function getDataForCache(): array
    {
        return $this->getInternationalizationBundlesGenerator()->getPackageNamespaces();
    }

    public function getInternationalizationBundlesGenerator(): InternationalizationBundlesGenerator
    {
        return $this->internationalizationBundlesGenerator;
    }

    /**
     * @return string[]
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function getPackageNamespaces(): array
    {
        return $this->loadCacheData();
    }
}