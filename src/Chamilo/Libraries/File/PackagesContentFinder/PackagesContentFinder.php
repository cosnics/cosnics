<?php
namespace Chamilo\Libraries\File\PackagesContentFinder;

use Chamilo\Libraries\File\PathBuilder;
use Exception;

/**
 * Abstract class that can be used to loop through a set of given packages and list content in an array based on
 * a set of conditions.
 * These conditions must be defined in the extensions of this class. Uses a PHP based cache.
 * For example: scan for directories with a given path, scan for files with a given path, scan for classes
 *
 * @package Chamilo\Libraries\File\PackagesContentFinder
 * @author Sven Vanpoucke - Hogeschool Gent
 */
abstract class PackagesContentFinder
{

    /**
     * The packages in which the system must be searching
     *
     * @var string[]
     */
    private $packages;

    /**
     * The location of the cache file
     *
     * @var string
     */
    private $cacheFile;

    /**
     * The path generator class
     *
     * @var \Chamilo\Libraries\File\PathBuilder
     */
    private $pathBuilder;

    /**
     * Constructor
     *
     * @param \Chamilo\Libraries\File\PathBuilder $pathBuilder
     * @param string[] $packages
     * @param string $cacheFile
     */
    public function __construct(PathBuilder $pathBuilder, array $packages = [], $cacheFile = null)
    {
        $this->packages = $packages;
        $this->cacheFile = $cacheFile;
        $this->pathBuilder = $pathBuilder;
    }

    /**
     * Locates the content, either from the given cache or by searching through the given set of packages
     *
     * @return string[][]
     * @throws \Exception
     */
    protected function findContent()
    {
        if (isset($this->cacheFile) && file_exists($this->cacheFile))
        {
            $content = require($this->cacheFile);

            if (!empty($content) && !is_array($content))
            {
                throw new Exception(
                    'The given cache file ' . $this->cacheFile . ' contains invalid data, should be an array'
                );
            }
        }
        else
        {
            $content = [];

            foreach ($this->packages as $package)
            {
                $content = array_merge($content, $this->handlePackage($package));
            }

            if (isset($this->cacheFile))
            {
                file_put_contents($this->cacheFile, sprintf('<?php return %s;', var_export($content, true)));
            }
        }

        return $content;
    }

    /**
     * Returns the full path to the given package
     *
     * @param string $package
     *
     * @return string
     */
    protected function getPackagePath($package)
    {
        return $this->pathBuilder->namespaceToFullPath($package);
    }

    /**
     * Handles a single package
     *
     * @param string $package
     *
     * @return string[]
     */
    abstract function handlePackage($package);
}