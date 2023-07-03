<?php
namespace Chamilo\Libraries\Cache;

use Chamilo\Libraries\Cache\Interfaces\CacheDataPreLoaderInterface;
use Chamilo\Libraries\File\ConfigurablePathBuilder;
use Exception;
use RuntimeException;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Abstract service class to manage caches that are file based
 *
 * @package Chamilo\Libraries\Cache
 * @author  Sven Vanpoucke - Hogeschool Gent
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
abstract class FileBasedCacheService implements CacheDataPreLoaderInterface
{
    protected ConfigurablePathBuilder $configurablePathBuilder;

    protected Filesystem $filesystem;

    public function __construct(
        ConfigurablePathBuilder $configurablePathBuilder, Filesystem $filesystem
    )
    {
        $this->configurablePathBuilder = $configurablePathBuilder;
        $this->filesystem = $filesystem;
    }

    public function clearCacheData(): bool
    {
        return $this->removeCachePath($this->getCachePath());
    }

    abstract public function getCachePath(): string;

    public function getConfigurablePathBuilder(): ConfigurablePathBuilder
    {
        return $this->configurablePathBuilder;
    }

    public function getFilesystem(): Filesystem
    {
        return $this->filesystem;
    }

    abstract public function initializeCache();

    public function preLoadCacheData(): mixed
    {
        if ($this->clearCacheData())
        {
            $this->initializeCache();
        }
    }

    protected function removeCachePath(string $cachePath): bool
    {
        if (file_exists($cachePath))
        {
            try
            {
                $this->getFilesystem()->remove($cachePath);
            }
            catch (Exception)
            {
                throw new RuntimeException(sprintf('Unable to remove the cache path "%s".', $cachePath));
            }
        }

        return true;
    }
}