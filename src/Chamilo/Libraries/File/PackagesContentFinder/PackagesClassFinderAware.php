<?php
namespace Chamilo\Libraries\File\PackagesContentFinder;

use InvalidArgumentException;

/**
 * Base class that can be used by other classes to include the PackagesClassFinder
 *
 * @package Chamilo\Libraries\File\PackagesContentFinder
 * @author Sven Vanpoucke - Hogeschool Gent
 */
abstract class PackagesClassFinderAware
{

    /**
     * The class finder for packages
     *
     * @var \Chamilo\Libraries\File\PackagesContentFinder\PackagesClassFinder
     */
    private $packagesClassFinder;

    /**
     * Constructor
     *
     * @param \Chamilo\Libraries\File\PackagesContentFinder\PackagesClassFinder $packagesClassFinder
     */
    public function __construct(PackagesClassFinder $packagesClassFinder = null)
    {
        $this->setPackagesClassFinder($packagesClassFinder);
    }

    /**
     *
     * @param \Chamilo\Libraries\File\PackagesContentFinder\PackagesClassFinder $packagesClassFinder
     * @throws \InvalidArgumentException
     */
    public function setPackagesClassFinder(PackagesClassFinder $packagesClassFinder)
    {
        if (! $packagesClassFinder instanceof PackagesClassFinder)
        {
            throw new InvalidArgumentException(
                'The given packages class finder should be an instance of' .
                     ' "\common\libraries\PackagesClassFinder", instead "' . get_class($packagesClassFinder) .
                     '" was given');
        }

        $this->packagesClassFinder = $packagesClassFinder;
    }

    /**
     *
     * @return \Chamilo\Libraries\File\PackagesContentFinder\PackagesClassFinder
     */
    public function getPackagesClassFinder()
    {
        return $this->packagesClassFinder;
    }
}