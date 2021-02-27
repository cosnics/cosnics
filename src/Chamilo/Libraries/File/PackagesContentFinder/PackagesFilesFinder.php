<?php
namespace Chamilo\Libraries\File\PackagesContentFinder;

use Symfony\Component\Finder\Finder;

/**
 * Finds files in a package based on a directory, with a given file pattern.
 * If no directory is given, the system
 * searches from the root of the package.
 * Uses a PHP-based caching system.
 * Class PackagesFilesFinder
 *
 * @package Chamilo\Libraries\File\PackagesContentFinder
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class PackagesFilesFinder extends PackagesContentFinder
{

    /**
     * The path relative to the root of the package that needs to be searched
     *
     * @var string
     */
    private $relativeFilePath;

    /**
     * The pattern of the filename
     *
     * @var string
     */
    private $filenamePattern;

    /**
     * Locates the files starting from a given directory, searching by a given pattern or filename, optionally using
     * recursive search.
     * - If no path is given, the search is started from the root of the package.
     * - If no filepattern is given, all the files are searched in the given directory
     *
     * @param string $relativeFilePath - The path relative to the package root
     * @param string $filenamePattern - The filename or the pattern of filenames
     * @return string[][]
     */
    public function findFiles($relativeFilePath = null, $filenamePattern = '')
    {
        $this->relativeFilePath = $relativeFilePath;
        $this->filenamePattern = $filenamePattern;

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
        $files = array();
        $path = $this->getPackagePath($package) . $this->relativeFilePath;

        if (! file_exists($path))
        {
            return $files;
        }

        $finder = new Finder();
        $finder->files()->depth(' == 0')->in($path);

        if ($this->filenamePattern)
        {
            $finder->name($this->filenamePattern);
        }

        foreach ($finder as $file)
        {
            $files[$package][] = $file->getRealPath();
        }

        return $files;
    }
}
