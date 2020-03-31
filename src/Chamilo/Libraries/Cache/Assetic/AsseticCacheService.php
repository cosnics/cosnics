<?php
namespace Chamilo\Libraries\Cache\Assetic;

use Assetic\Asset\AssetCache;
use Assetic\Asset\AssetCollection;
use Assetic\Cache\FilesystemCache;
use Chamilo\Libraries\Cache\Interfaces\CacheResetterInterface;
use Chamilo\Libraries\File\ConfigurablePathBuilder;
use Chamilo\Libraries\File\Filesystem;
use Chamilo\Libraries\File\PathBuilder;
use Exception;

/**
 *
 * @package Chamilo\Libraries\Cache\Assetic
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
abstract class AsseticCacheService implements CacheResetterInterface
{

    /**
     *
     * @var \Assetic\Cache\FilesystemCache
     */
    private $filesystemCache;

    /**
     *
     * @var \Chamilo\Libraries\File\PathBuilder
     */
    private $pathBuilder;

    /**
     *
     * @var \Chamilo\Libraries\File\ConfigurablePathBuilder
     */
    private $configurablePathBuilder;

    /**
     *
     * @var \Assetic\Asset\AssetCache
     */
    private $assetCache;

    /**
     *
     * @param \Chamilo\Libraries\File\PathBuilder $pathBuilder
     * @param \Chamilo\Libraries\File\ConfigurablePathBuilder $configurablePathBuilder
     */
    public function __construct(PathBuilder $pathBuilder, ConfigurablePathBuilder $configurablePathBuilder)
    {
        $this->pathBuilder = $pathBuilder;
        $this->configurablePathBuilder = $configurablePathBuilder;
    }

    /**
     *
     * @see \Chamilo\Libraries\Cache\Interfaces\CacheClearerInterface::clear()
     */
    public function clear()
    {
        return Filesystem::remove($this->getCachePath());
    }

    /**
     *
     * @see \Chamilo\Libraries\Cache\Interfaces\CacheResetterInterface::clearAndWarmUp()
     */
    public function clearAndWarmUp()
    {
        if (!$this->clear())
        {
            return false;
        }

        return $this->warmUp();
    }

    /**
     *
     * @return string
     */
    public function get()
    {
        return $this->getAssetCache()->dump();
    }

    /**
     *
     * @return \Assetic\Asset\AssetCache
     */
    protected function getAssetCache()
    {
        if (!isset($this->assetCache))
        {
            $this->assetCache = new AssetCache($this->getAssetCollection(), $this->getFilesystemCache());
        }

        return $this->assetCache;
    }

    /**
     *
     * @return \Assetic\Asset\AssetCollection
     */
    protected function getAssetCollection()
    {
        if (!isset($this->assetCollection))
        {
            $this->assetCollection =
                new AssetCollection($this->getAssets(), $this->getAssetFilters(), null, $this->getAssetVariables());
        }

        return $this->assetCollection;
    }

    /**
     *
     * @return \Assetic\Filter\FilterInterface[]
     */
    abstract protected function getAssetFilters();

    /**
     *
     * @return string[]
     */
    abstract protected function getAssetVariables();

    /**
     *
     * @return \Assetic\Asset\FileAsset[]
     */
    abstract protected function getAssets();

    /**
     *
     * @return string
     */
    abstract protected function getCachePath();

    /**
     *
     * @return \Chamilo\Libraries\File\ConfigurablePathBuilder
     */
    public function getConfigurablePathBuilder()
    {
        return $this->configurablePathBuilder;
    }

    /**
     *
     * @param \Chamilo\Libraries\File\ConfigurablePathBuilder $configurablePathBuilder
     */
    public function setConfigurablePathBuilder(ConfigurablePathBuilder $configurablePathBuilder)
    {
        $this->configurablePathBuilder = $configurablePathBuilder;
    }

    /**
     *
     * @return \Assetic\Cache\FilesystemCache
     */
    public function getFilesystemCache()
    {
        if (!isset($this->filesystemCache))
        {
            $this->filesystemCache = new FilesystemCache($this->getCachePath());
        }

        return $this->filesystemCache;
    }

    /**
     *
     * @param \Assetic\Cache\FilesystemCache $filesystemCache
     */
    public function setFilesystemCache($filesystemCache)
    {
        $this->filesystemCache = $filesystemCache;
    }

    /**
     * Returns the last modification time of the resource
     *
     * @return integer
     */
    public function getLastModificationTime()
    {
        if (!file_exists($this->getCachePath()))
        {
            return 0;
        }

        return filemtime($this->getCachePath());
    }

    /**
     *
     * @return \Chamilo\Libraries\File\PathBuilder
     */
    public function getPathBuilder()
    {
        return $this->pathBuilder;
    }

    /**
     *
     * @param \Chamilo\Libraries\File\PathBuilder $pathBuilder
     */
    public function setPathBuilder(PathBuilder $pathBuilder)
    {
        $this->pathBuilder = $pathBuilder;
    }

    /**
     *
     * @see \Chamilo\Libraries\Cache\Interfaces\CacheWarmerInterface::warmUp()
     */
    public function warmUp()
    {
        try
        {
            $this->getAssetCache()->dump();

            return true;
        }
        catch (Exception $exception)
        {
            return false;
        }
    }
}