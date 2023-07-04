<?php
namespace Chamilo\Configuration\Package\Service;

use Chamilo\Configuration\Package\Finder\PackageBundlesGenerator;
use Chamilo\Configuration\Package\PackageList;
use Chamilo\Libraries\Cache\Interfaces\CacheDataPreLoaderInterface;
use Chamilo\Libraries\Cache\Traits\CacheAdapterHandlerTrait;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\Cache\Exception\CacheException;

/**
 * @package Chamilo\Configuration\Package\Service
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class PackageBundlesCacheService implements CacheDataPreLoaderInterface
{
    use CacheAdapterHandlerTrait;

    protected PackageBundlesGenerator $packageBundlesGenerator;

    public function __construct(AdapterInterface $cacheAdapter, PackageBundlesGenerator $packageBundlesGenerator)
    {
        $this->cacheAdapter = $cacheAdapter;
        $this->packageBundlesGenerator = $packageBundlesGenerator;
    }

    /**
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function getAllPackages(): PackageList
    {
        if (!$this->loadCacheDataForIdentifier(PackageList::MODE_ALL))
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
        if (!$this->loadCacheDataForIdentifier(PackageList::MODE_AVAILABLE))
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
        if (!$this->loadCacheDataForIdentifier(PackageList::MODE_INSTALLED))
        {
            throw new CacheException(
                'Could not load cache for ' . __CLASS__ . ' with key ' . PackageList::MODE_INSTALLED
            );
        }

        return $this->readCacheDataForKey($this->getCacheKeyForParts([PackageList::MODE_INSTALLED]));
    }

    public function getPackageBundlesGenerator(): PackageBundlesGenerator
    {
        return $this->packageBundlesGenerator;
    }

    /**
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function getPackageListForMode(int $mode): PackageList
    {
        $packageBundlesGenerator = $this->getPackageBundlesGenerator();
        $packageList = $packageBundlesGenerator->getPackageListForNamespaceAndMode(PackageList::ROOT, $mode);

        $packageList->getAllPackages(false);
        $packageList->getList(false);

        $packageList->getAllPackages();
        $packageList->getList();

        return $packageList;
    }

    public function loadCacheDataForIdentifier(int $cacheIdentifier): bool
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

    public function preLoadCacheData(): mixed
    {
        foreach ($this->getCacheIdentifiers() as $cacheIdentifier)
        {
            if (!$this->loadCacheDataForIdentifier($cacheIdentifier))
            {
                return false;
            }
        }

        return true;
    }
}