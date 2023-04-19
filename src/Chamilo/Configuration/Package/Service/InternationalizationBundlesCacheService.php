<?php
namespace Chamilo\Configuration\Package\Service;

use Chamilo\Configuration\Package\Finder\InternationalizationBundles;
use Chamilo\Configuration\Package\PackageList;
use Chamilo\Libraries\Cache\Interfaces\CacheDataLoaderInterface;
use Chamilo\Libraries\Cache\Traits\CacheDataLoaderTrait;
use Symfony\Component\Cache\Adapter\AdapterInterface;

/**
 * @package Chamilo\Configuration\Package\Service
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class InternationalizationBundlesCacheService implements CacheDataLoaderInterface
{
    use CacheDataLoaderTrait;

    public function __construct(AdapterInterface $cacheAdapter)
    {
        $this->cacheAdapter = $cacheAdapter;
    }

    /**
     * @return string[]
     */
    protected function getDataForCache(): array
    {
        $internationalizationBundles = new InternationalizationBundles(PackageList::ROOT);

        return $internationalizationBundles->getPackageNamespaces();
    }
}