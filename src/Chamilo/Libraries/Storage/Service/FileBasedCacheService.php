<?php
namespace Chamilo\Libraries\Storage\Service;

use Chamilo\Libraries\Filesystem\Service\ConfigurablePathBuilder;
use Chamilo\Libraries\Storage\Architecture\Interface\CacheDataPreLoaderInterface;
use Exception;
use RuntimeException;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Abstract service class to manage caches that are file based
 *
 * @package Chamilo\Libraries\Storage\Service
 * @author  Sven Vanpoucke - Hogeschool Gent
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
abstract class FileBasedCacheService implements CacheDataPreLoaderInterface
{
    public function __construct(
        protected ConfigurablePathBuilder $configurablePathBuilder, protected Filesystem $filesystem
    )
    {
    }

    public function clearCacheData(): bool
    {
        return $this->removeCachePath($this->getCachePath());
    }

    abstract public function getCachePath(): string;

    abstract public function initializeCache();

    public function preLoadCacheData(): void
    {
        if ($this->clearCacheData()) {
            $this->initializeCache();
        }
    }

    protected function removeCachePath(string $cachePath): bool
    {
        if (file_exists($cachePath)) {
            try {
                $this->filesystem->remove($cachePath);
            }
            catch (Exception) {
                throw new RuntimeException(sprintf('Unable to remove the cache path "%s".', $cachePath));
            }
        }

        return true;
    }
}