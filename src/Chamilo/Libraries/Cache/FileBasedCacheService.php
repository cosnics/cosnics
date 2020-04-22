<?php
namespace Chamilo\Libraries\Cache;

use Chamilo\Libraries\Cache\Interfaces\CacheResetterInterface;
use Chamilo\Libraries\File\Filesystem;
use RuntimeException;

/**
 * Abstract service class to manage caches that are file based
 *
 * @package Chamilo\Libraries\Cache
 * @author Sven Vanpoucke - Hogeschool Gent
 */
abstract class FileBasedCacheService implements CacheResetterInterface
{

    /**
     * Clears the cache.
     */
    public function clear()
    {
        return $this->removeCachePath($this->getCachePath());
    }

    /**
     * Clears the cache and warms it up again
     *
     * @return \Chamilo\Libraries\Cache\FileBasedCacheService
     */
    public function clearAndWarmUp()
    {
        return $this->clear()->warmUp();
    }

    /**
     * Returns the path to the cache directory or file
     *
     * @return string
     */
    abstract function getCachePath();

    /**
     * Removes the cachePath
     *
     * @param string $cachePath
     *
     * @return $this
     */
    protected function removeCachePath($cachePath)
    {
        if (file_exists($cachePath))
        {
            if (!Filesystem::remove($cachePath))
            {
                throw new RuntimeException(sprintf('Unable to remove the cache path "%s".', $cachePath));
            }
        }

        return $this;
    }

    /**
     * Warms up the cache
     *
     * @return \Chamilo\Libraries\Cache\FileBasedCacheService
     */
    public function warmUp()
    {
        return $this;
    }
}