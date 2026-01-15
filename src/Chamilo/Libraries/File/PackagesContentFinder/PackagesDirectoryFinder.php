<?php
namespace Chamilo\Libraries\File\PackagesContentFinder;

/**
 * Finds directories in a package based on a given directory name.
 * Uses a PHP-based caching system.
 *
 * @package Chamilo\Libraries\File\PackagesContentFinder
 * @author  Sven Vanpoucke - Hogeschool Gent
 */
class PackagesDirectoryFinder extends PackagesContentFinder
{

    private string $relativeFilePath;

    /**
     * Locates the directories by a given filepath.
     * Checks for each package if the path exists.
     *
     * @param string $relativeFilePath - The path relative to the package root
     *
     * @return string[]
     * @throws \Exception
     */
    public function findDirectories(string $relativeFilePath): array
    {
        $this->relativeFilePath = $relativeFilePath;

        return $this->findContent();
    }

    /**
     * @param string $package
     *
     * @return string[]
     */
    public function handlePackage(string $package): array
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