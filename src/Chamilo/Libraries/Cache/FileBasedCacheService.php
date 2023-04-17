<?php
namespace Chamilo\Libraries\Cache;

use Chamilo\Libraries\Cache\Interfaces\CacheInterface;
use Chamilo\Libraries\File\Filesystem;
use RuntimeException;

/**
 * Abstract service class to manage caches that are file based
 *
 * @package Chamilo\Libraries\Cache
 * @author  Sven Vanpoucke - Hogeschool Gent
 */
abstract class FileBasedCacheService implements CacheInterface
{

    public function clear(): bool
    {
        return $this->removeCachePath($this->getCachePath());
    }

    public function clearAndWarmUp(): bool
    {
        if (!$this->clear())
        {
            return false;
        }

        return $this->warmUp();
    }

    abstract public function getCachePath(): string;

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