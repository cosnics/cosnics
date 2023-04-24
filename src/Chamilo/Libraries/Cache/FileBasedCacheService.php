<?php
namespace Chamilo\Libraries\Cache;

use Chamilo\Libraries\Cache\Interfaces\CacheDataPreLoaderInterface;
use Chamilo\Libraries\File\ConfigurablePathBuilder;
use Chamilo\Libraries\File\Filesystem;
use RuntimeException;

/**
 * Abstract service class to manage caches that are file based
 *
 * @package Chamilo\Libraries\Cache
 * @author  Sven Vanpoucke - Hogeschool Gent
 */
abstract class FileBasedCacheService implements CacheDataPreLoaderInterface
{
    protected ConfigurablePathBuilder $configurablePathBuilder;

    public function __construct(ConfigurablePathBuilder $configurablePathBuilder)
    {
        $this->configurablePathBuilder = $configurablePathBuilder;
    }

    public function clear(): bool
    {
        return $this->removeCachePath($this->getCachePath());
    }

    abstract public function getCachePath(): string;

    public function getConfigurablePathBuilder(): ConfigurablePathBuilder
    {
        return $this->configurablePathBuilder;
    }

    protected function removeCachePath(string $cachePath): bool
    {
        if (file_exists($cachePath))
        {
            if (!Filesystem::remove($cachePath))
            {
                throw new RuntimeException(sprintf('Unable to remove the cache path "%s".', $cachePath));
            }
        }

        return true;
    }
}