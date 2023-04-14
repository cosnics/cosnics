<?php
namespace Chamilo\Configuration\Package\Service;

use Chamilo\Configuration\Package\Finder\PackageBundles;
use Chamilo\Configuration\Package\PackageList;
use Chamilo\Libraries\Cache\Doctrine\DoctrineCacheService;
use Chamilo\Libraries\File\ConfigurablePathBuilder;
use Symfony\Component\Cache\Adapter\AdapterInterface;

/**
 * @package Chamilo\Configuration\Package\Service
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class PackageBundlesCacheService extends DoctrineCacheService
{
    protected PackageFactory $packageFactory;

    public function __construct(
        AdapterInterface $cacheAdapter, ConfigurablePathBuilder $configurablePathBuilder, PackageFactory $packageFactory
    )
    {
        parent::__construct($cacheAdapter, $configurablePathBuilder);

        $this->packageFactory = $packageFactory;
    }

    /**
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function getAllPackages(): PackageList
    {
        return $this->getForIdentifier(PackageList::MODE_ALL);
    }

    /**
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function getAvailablePackages(): PackageList
    {
        return $this->getForIdentifier(PackageList::MODE_AVAILABLE);
    }

    /**
     * @return string[]
     */
    public function getIdentifiers(): array
    {
        return [PackageList::MODE_ALL, PackageList::MODE_INSTALLED, PackageList::MODE_AVAILABLE];
    }

    /**
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function getInstalledPackages(): PackageList
    {
        return $this->getForIdentifier(PackageList::MODE_INSTALLED);
    }

    public function getPackageFactory(): PackageFactory
    {
        return $this->packageFactory;
    }

    /**
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function warmUpForIdentifier($identifier): bool
    {
        $packageFactory = $this->getPackageFactory();
        $packageListBuilder = new PackageBundles(PackageList::ROOT, $identifier, $packageFactory);
        $packageList = $packageListBuilder->getPackageList();

        $packageList->get_all_packages(false);
        $packageList->get_list(false);

        $packageList->get_all_packages();
        $packageList->get_list();

        $cacheItem = $this->getCacheAdapter()->getItem($identifier);
        $cacheItem->set($packageList);

        return $this->getCacheAdapter()->save($cacheItem);
    }
}