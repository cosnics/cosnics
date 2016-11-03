<?php
namespace Chamilo\Configuration\Package\Service;

use Chamilo\Configuration\Package\Finder\PackageBundles;
use Chamilo\Configuration\Package\PackageList;
use Chamilo\Libraries\Cache\Doctrine\Service\DoctrineFilesystemCacheService;

/**
 *
 * @package Chamilo\Configuration\Package\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class PackageBundlesCacheService extends DoctrineFilesystemCacheService
{

    /**
     *
     * @see \Chamilo\Libraries\Cache\Doctrine\DoctrineCacheService::getCachePathNamespace()
     */
    public function getCachePathNamespace()
    {
        return 'Chamilo\Configuration\Package\PlatformPackageBundles';
    }

    /**
     *
     * @see \Chamilo\Libraries\Cache\IdentifiableCacheService::warmUpForIdentifier()
     */
    public function warmUpForIdentifier($identifier)
    {
        $packageListBuilder = new PackageBundles(PackageList :: ROOT, $identifier);

        $packageList = $packageListBuilder->getPackageList();
        $packageList->get_all_packages();
        $packageList->get_all_packages(true);
        $packageList->get_list();
        $packageList->get_list(true);

        return $this->getCacheProvider()->save($identifier, $packageList);
    }

    /**
     *
     * @see \Chamilo\Libraries\Cache\IdentifiableCacheService::getIdentifiers()
     */
    public function getIdentifiers()
    {
        return array(PackageList :: MODE_ALL, PackageList :: MODE_INSTALLED, PackageList :: MODE_AVAILABLE);
    }

    /**
     *
     * @return \Chamilo\Configuration\Package\PackageList
     */
    public function getAllPackages()
    {
        return $this->getForIdentifier(PackageList :: MODE_ALL);
    }

    /**
     *
     * @return \Chamilo\Configuration\Package\PackageList
     */
    public function getInstalledPackages()
    {
        return $this->getForIdentifier(PackageList :: MODE_INSTALLED);
    }

    /**
     *
     * @return \Chamilo\Configuration\Package\PackageList
     */
    public function getAvailablePackages()
    {
        return $this->getForIdentifier(PackageList :: MODE_AVAILABLE);
    }
}