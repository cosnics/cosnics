<?php
namespace Chamilo\Libraries\Filesystem\Service\PackagesContentFinder;

/**
 * Finds classes in packages based on a filename and classname.
 * Uses a PHP-based caching system.
 *
 * @package Chamilo\Libraries\File\PackagesContentFinder
 * @author  Sven Vanpoucke - Hogeschool Gent
 */
class PackagesClassFinder extends PackagesContentFinder
{

    private string $className;

    private string $relativeFilePath;

    /**
     * Locates the classes by a given filepath and classname.
     * Checks for each package if the path and the class exists.
     *
     * @param string $relativeFilePath - The path relative to the package root
     * @param string $className - The classname relative to the package namespace (not the fully qualified one)
     *
     * @return string[]
     * @throws \Exception
     */
    public function findClasses(string $relativeFilePath, string $className): array
    {
        $this->relativeFilePath = $relativeFilePath;
        $this->className = $className;

        return $this->findContent();
    }

    public function handlePackage(string $package): array
    {
        $classes = [];

        $path = $this->getPackagePath($package) . $this->relativeFilePath;
        if (file_exists($path))
        {
            $class = $package . '\\' . $this->className;
            if (class_exists($class))
            {
                $classes[$package] = $class;
            }
        }

        return $classes;
    }
}