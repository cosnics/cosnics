<?php
namespace Chamilo\Libraries\Cache\Assetic;

use Assetic\Asset\AssetCache;
use Assetic\Asset\AssetCollection;
use Assetic\Cache\FilesystemCache;
use Chamilo\Libraries\Cache\Interfaces\CacheResetterInterface;
use Chamilo\Libraries\File\ConfigurablePathBuilder;
use Chamilo\Libraries\File\Filesystem;
use Chamilo\Libraries\File\PathBuilder;

/**
 *
 * @package Chamilo\Libraries\Format\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
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
     * @return string
     */
    abstract protected function getCachePath();

    /**
     *
     * @return \Assetic\Cache\FilesystemCache
     */
    public function getFilesystemCache()
    {
        if (! isset($this->filesystemCache))
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
     *
     * @return \Assetic\Asset\FileAsset[]
     */
    abstract protected function getAssets();

    /**
     *
     * @return \Assetic\Filter\FilterInterface[]
     */
    abstract protected function getAssetFilters();

    /**
     *
     * @return \Assetic\Asset\AssetCollection
     */
    protected function getAssetCollection()
    {
        if (! isset($this->assetCollection))
        {
            $this->assetCollection = new AssetCollection($this->getAssets(), $this->getAssetFilters());
        }

        return $this->assetCollection;
    }

    /**
     *
     * @return \Assetic\Asset\AssetCache
     */
    protected function getAssetCache()
    {
        if (! isset($this->assetCache))
        {
            $this->assetCache = new AssetCache($this->getAssetCollection(), $this->getFilesystemCache());
        }

        return $this->assetCache;
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
        catch (\Exception $exception)
        {
            return false;
        }
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
        if (! $this->clear())
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
     * Returns the last modification time of the resource
     *
     * @return int
     */
    public function getLastModificationTime()
    {
        if (! file_exists($this->getCachePath()))
        {
            return 0;
        }

        return filemtime($this->getCachePath());
    }
}