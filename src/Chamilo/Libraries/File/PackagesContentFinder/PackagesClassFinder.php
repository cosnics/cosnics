<?php
namespace Chamilo\Libraries\File\PackagesContentFinder;

/**
 * Finds classes in packages based on a filename and classname.
 * Uses a PHP-based caching system.
 *
 * @package Chamilo\Libraries\File\PackagesContentFinder
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class PackagesClassFinder extends PackagesContentFinder
{

    /**
     * The path relative to the root of the package that needs to be searched
     *
     * @var string
     */
    private $relativeFilePath;

    /**
     * The class name that needs to be searched
     *
     * @var string
     */
    private $className;

    /**
     * Locates the classes by a given filepath and classname.
     * Checks for each package if the path and the class exists.
     *
     * @param string $relativeFilePath - The path relative to the package root
     * @param string $className - The classname relative to the package namespace (not the fully qualified one)
     * @return string[]
     */
    public function findClasses($relativeFilePath, $className)
    {
        $this->relativeFilePath = $relativeFilePath;
        $this->className = $className;

        return $this->findContent();
    }

    /**
     * Handles a single package
     *
     * @param string $package
     * @return string[]
     */
    function handlePackage($package)
    {
        $classes = array();

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