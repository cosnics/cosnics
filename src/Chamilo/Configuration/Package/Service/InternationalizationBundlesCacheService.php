<?php
namespace Chamilo\Configuration\Package\Service;

use Chamilo\Configuration\Package\Finder\InternationalizationBundles;
use Chamilo\Configuration\Package\PackageList;
use Chamilo\Libraries\Cache\Doctrine\Service\DoctrineFilesystemCacheService;

/**
 * @package Chamilo\Configuration\Package\Service
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class InternationalizationBundlesCacheService extends DoctrineFilesystemCacheService
{

    /**
     * @return \Chamilo\Configuration\Package\PackageList
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function getAllPackages(): PackageList
    {
        return $this->getForIdentifier(PackageList::MODE_ALL);
    }

    /**
     * @return string[]
     */
    public function getIdentifiers(): array
    {
        return [PackageList::MODE_ALL];
    }

    /**
     * @throws \Psr\Cache\InvalidArgumentException
     * @see \Chamilo\Libraries\Cache\IdentifiableCacheService::warmUpForIdentifier()
     */
    public function warmUpForIdentifier($identifier): bool
    {
        $internationalizationBundles = new InternationalizationBundles(PackageList::ROOT);

        $cacheItem = $this->getCacheAdapter()->getItem((string) $identifier);
        $cacheItem->set($internationalizationBundles->getPackageNamespaces());

        return $this->getCacheAdapter()->save($cacheItem);
    }
}