<?php
namespace Chamilo\Libraries\Filesystem\Service\PackagesContentFinder;

use Chamilo\Libraries\Filesystem\Service\SystemPathBuilder;
use Exception;

/**
 * Abstract class that can be used to loop through a set of given packages and list content in an array based on
 * a set of conditions.
 * These conditions must be defined in the extensions of this class. Uses a PHP based cache.
 * For example: scan for directories with a given path, scan for files with a given path, scan for classes
 *
 * @package Chamilo\Libraries\File\PackagesContentFinder
 * @author  Sven Vanpoucke - Hogeschool Gent
 */
abstract class PackagesContentFinder
{

    private ?string $cacheFilePath;

    /**
     * The packages in which the system must be searching
     *
     * @var string[]
     */
    private array $packages;

    private SystemPathBuilder $systemPathBuilder;

    /**
     * @param \Chamilo\Libraries\Filesystem\Service\SystemPathBuilder $systemPathBuilder
     * @param string[] $packages
     * @param ?string $cacheFilePath
     */
    public function __construct(
        SystemPathBuilder $systemPathBuilder, array $packages = [], ?string $cacheFilePath = null
    )
    {
        $this->packages = $packages;
        $this->cacheFilePath = $cacheFilePath;
        $this->systemPathBuilder = $systemPathBuilder;
    }

    /**
     * Locates the content, either from the given cache or by searching through the given set of packages
     *
     * @return string[][]
     * @throws \Exception
     */
    protected function findContent(): array
    {
        $cacheFilePath = $this->getCacheFilePath();

        if (isset($cacheFilePath) && file_exists($cacheFilePath))
        {
            $content = require($cacheFilePath);

            if (!empty($content) && !is_array($content))
            {
                throw new Exception(
                    'The given cache file ' . $cacheFilePath . ' contains invalid data, should be an array'
                );
            }
        }
        else
        {
            $content = [];

            foreach ($this->getPackages() as $package)
            {
                $content = array_merge($content, $this->handlePackage($package));
            }

            if (isset($cacheFilePath))
            {
                file_put_contents($cacheFilePath, sprintf('<?php return %s;', var_export($content, true)));
            }
        }

        return $content;
    }

    public function getCacheFilePath(): ?string
    {
        return $this->cacheFilePath;
    }

    protected function getPackagePath(string $package): string
    {
        return $this->getSystemPathBuilder()->namespaceToFullPath($package);
    }

    public function getPackages(): array
    {
        return $this->packages;
    }

    public function getSystemPathBuilder(): SystemPathBuilder
    {
        return $this->systemPathBuilder;
    }

    /**
     * @param string $package
     *
     * @return string[]
     */
    abstract public function handlePackage(string $package): array;
}