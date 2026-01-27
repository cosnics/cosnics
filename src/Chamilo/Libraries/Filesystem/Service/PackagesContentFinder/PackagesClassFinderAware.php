<?php
namespace Chamilo\Libraries\Filesystem\Service\PackagesContentFinder;

/**
 * Base class that can be used by other classes to include the PackagesClassFinder
 *
 * @package Chamilo\Libraries\File\PackagesContentFinder
 * @author Sven Vanpoucke - Hogeschool Gent
 */
abstract class PackagesClassFinderAware
{

    private PackagesClassFinder $packagesClassFinder;

    public function __construct(PackagesClassFinder $packagesClassFinder = null)
    {
        $this->setPackagesClassFinder($packagesClassFinder);
    }

    public function getPackagesClassFinder(): PackagesClassFinder
    {
        return $this->packagesClassFinder;
    }

    public function setPackagesClassFinder(PackagesClassFinder $packagesClassFinder): void
    {
        $this->packagesClassFinder = $packagesClassFinder;
    }
}