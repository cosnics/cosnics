<?php
namespace Chamilo\Configuration\Package\Service;

use Chamilo\Configuration\Package\Finder\PackageBundles;
use Chamilo\Configuration\Package\PackageList;
use Chamilo\Libraries\Cache\Interfaces\CacheDataLoaderInterface;
use Chamilo\Libraries\Cache\Traits\CacheAdapterHandlerTrait;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\Cache\Exception\CacheException;

/**
 * @package Chamilo\Configuration\Package\Service
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class PackageBundlesCacheService implements CacheDataLoaderInterface
{
    use CacheAdapterHandlerTrait;

    protected PackageFactory $packageFactory;

    public function __construct(AdapterInterface $cacheAdapter, PackageFactory $packageFactory)
    {
        $this->cacheAdapter = $cacheAdapter;
        $this->packageFactory = $packageFactory;
    }

    /**
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function getAllPackages(): PackageList
    {
        if (!$this->loadCacheDataForIdentifier((string) PackageList::MODE_ALL))
        {
            throw new CacheException(
                'Could not load cache for ' . __CLASS__ . ' with key ' . PackageList::MODE_ALL
            );
        }

        return $this->readCacheDataForKey($this->getCacheKeyForParts([PackageList::MODE_ALL]));
    }

    /**
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function getAvailablePackages(): PackageList
    {
        if (!$this->loadCacheDataForIdentifier((string) PackageList::MODE_AVAILABLE))
        {
            throw new CacheException(
                'Could not load cache for ' . __CLASS__ . ' with key ' . PackageList::MODE_AVAILABLE
            );
        }

        return $this->readCacheDataForKey($this->getCacheKeyForParts([PackageList::MODE_AVAILABLE]));
    }

    /**
     * @return int[]
     */
    protected function getCacheIdentifiers(): array
    {
        return [PackageList::MODE_ALL, PackageList::MODE_INSTALLED, PackageList::MODE_AVAILABLE];
    }

    /**
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function getInstalledPackages(): PackageList
    {
        if (!$this->loadCacheDataForIdentifier((string) PackageList::MODE_INSTALLED))
        {
            throw new CacheException(
                'Could not load cache for ' . __CLASS__ . ' with key ' . PackageList::MODE_INSTALLED
            );
        }

        return $this->readCacheDataForKey($this->getCacheKeyForParts([PackageList::MODE_INSTALLED]));
    }

    public function getPackageFactory(): PackageFactory
    {
        return $this->packageFactory;
    }

    public function getPackageListForMode(string $mode): PackageList
    {
        $packageFactory = $this->getPackageFactory();
        $packageListBuilder = new PackageBundles(PackageList::ROOT, $mode, $packageFactory);
        $packageList = $packageListBuilder->getPackageList();

        $packageList->get_all_packages(false);
        $packageList->get_list(false);

        $packageList->get_all_packages();
        $packageList->get_list();

        return $packageList;
    }

    public function loadCachedData()
    {
        foreach ($this->getCacheIdentifiers() as $cacheIdentifier)
        {
            if (!$this->loadCacheDataForIdentifier((string) $cacheIdentifier))
            {
                return false;
            }
        }

        return true;
    }

    public function loadCacheDataForIdentifier(string $cacheIdentifier): bool
    {
        $cacheKey = $this->getCacheKeyForParts([$cacheIdentifier]);

        if (!$this->hasCacheDataForKey($cacheKey))
        {
            try
            {
                if (!$this->saveCacheDataForKey($cacheKey, $this->getPackageListForMode($cacheIdentifier)))
                {
                    return false;
                }
            }
            catch (CacheException $e)
            {
                return false;
            }
        }

        return true;
    }
}