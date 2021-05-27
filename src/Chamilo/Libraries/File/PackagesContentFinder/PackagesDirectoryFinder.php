<?php
namespace Chamilo\Libraries\File\PackagesContentFinder;

/**
 * Finds directories in a package based on a given directory name.
 * Uses a PHP-based caching system.
 *
 * @package Chamilo\Libraries\File\PackagesContentFinder
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class PackagesDirectoryFinder extends PackagesContentFinder
{

    /**
     * The path relative to the root of the package that needs to be searched
     *
     * @var string
     */
    private $relativeFilePath;

    /**
     * Locates the directories by a given filepath.
     * Checks for each package if the path exists.
     *
     * @param string $relativeFilePath - The path relative to the package root
     *
     * @return string[]
     * @throws \Exception
     */
    public function findDirectories($relativeFilePath)
    {
        $this->relativeFilePath = $relativeFilePath;

        return $this->findContent();
    }

    /**
     * Handles a single package
     *
     * @param string $package
     *
     * @return string[]
     */
    function handlePackage($package)
    {
        $directories = [];

        $path = $this->getPackagePath($package) . $this->relativeFilePath;
        if (file_exists($path))
        {
            $directories[$package] = $path;
        }

        return $directories;
    }
}