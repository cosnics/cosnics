<?php
namespace Chamilo\Libraries\Filesystem\Service\PackagesContentFinder;

/**
 * Base class that can be used by other classes to include the PackagesClassFinder
 *
 * @package Chamilo\Libraries\Filesystem\Service\PackagesContentFinder
 * @author Sven Vanpoucke - Hogeschool Gent
 */
abstract class PackagesClassFinderAware
{
    public function __construct(protected ?PackagesClassFinder $packagesClassFinder = null)
    {
    }
}