<?php
namespace Chamilo\Libraries\Cache\Assetic;

use Assetic\Asset\AssetCache;
use Assetic\Cache\FilesystemCache;
use Chamilo\Libraries\Cache\Interfaces\CacheResetterInterface;
use Chamilo\Libraries\File\Path;
use Assetic\Asset\AssetCollection;
use Chamilo\Libraries\File\Filesystem;

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
     * @var \Chamilo\Libraries\File\Path
     */
    private $pathUtilities;

    /**
     *
     * @var \Assetic\Asset\AssetCache
     */
    private $assetCache;

    /**
     *
     * @param \Chamilo\Libraries\File\Path $pathUtilities
     */
    public function __construct(Path $pathUtilities)
    {
        $this->pathUtilities = $pathUtilities;
    }

    /**
     *
     * @return \Chamilo\Libraries\File\Path
     */
    public function getPathUtilities()
    {
        return $this->pathUtilities;
    }

    /**
     *
     * @param \Chamilo\Libraries\File\Path $pathUtilities
     */
    public function setPathUtilities(Path $pathUtilities)
    {
        $this->pathUtilities = $pathUtilities;
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
        return Filesystem :: remove($this->getCachePath());
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
        if(!file_exists($this->getCachePath()))
        {
            return 0;
        }

        return filemtime($this->getCachePath());
    }
}