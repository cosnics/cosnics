<?php
namespace Chamilo\Configuration\Package\Service;

use Chamilo\Configuration\Package\Finder\InternationalizationBundles;
use Chamilo\Configuration\Package\PackageList;
use Chamilo\Libraries\Cache\Doctrine\Service\DoctrineFilesystemCacheService;

/**
 *
 * @package Chamilo\Configuration\Package\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class InternationalizationBundlesCacheService extends DoctrineFilesystemCacheService
{

    /**
     *
     * @see \Chamilo\Libraries\Cache\Doctrine\DoctrineCacheService::getCachePathNamespace()
     */
    public function getCachePathNamespace()
    {
        return 'Chamilo\Configuration\Package\InternationalizationBundles';
    }

    /**
     *
     * @see \Chamilo\Libraries\Cache\IdentifiableCacheService::warmUpForIdentifier()
     */
    public function warmUpForIdentifier($identifier)
    {
        $internationalizationBundles = new InternationalizationBundles(PackageList::ROOT);
        return $this->getCacheProvider()->save($identifier, $internationalizationBundles->getPackageNamespaces());
    }

    /**
     *
     * @see \Chamilo\Libraries\Cache\IdentifiableCacheService::getIdentifiers()
     */
    public function getIdentifiers()
    {
        return array(PackageList::MODE_ALL);
    }

    /**
     *
     * @return \Chamilo\Configuration\Package\PackageList
     */
    public function getAllPackages()
    {
        return $this->getForIdentifier(PackageList::MODE_ALL);
    }
}