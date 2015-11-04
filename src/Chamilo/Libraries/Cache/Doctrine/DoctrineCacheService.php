<?php
namespace Chamilo\Libraries\Cache\Doctrine;

use Chamilo\Libraries\Cache\CacheServiceInterface;
use Chamilo\Libraries\File\Path;

/**
 *
 * @package Chamilo\Libraries\Cache\Doctrine
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class DoctrineCacheService implements CacheServiceInterface
{

    /**
     *
     * @var \Doctrine\Common\Cache\CacheProvider
     */
    private $cacheProvider;

    /**
     *
     * @return string
     */
    abstract function getCacheIdentifiers();

    /**
     *
     * @return string
     */
    abstract function getCachePathNamespace();

    /**
     *
     * @return string
     */
    protected function getCachePath()
    {
        return Path :: getInstance()->getCachePath($this->getCachePathNamespace());
    }

    /**
     *
     * @see \Chamilo\Libraries\Cache\CacheServiceInterface::getCacheProvider()
     */
    public function getCacheProvider()
    {
        if (! isset($this->cacheProvider))
        {
            $this->cacheProvider = $this->setupCacheProvider();
        }

        return $this->cacheProvider;
    }

    /**
     *
     * @param \Doctrine\Common\Cache\CacheProvider $cacheProvider
     */
    public function setCacheProvider($cacheProvider)
    {
        $this->cacheProvider = $cacheProvider;
    }

    /**
     *
     * @return \Doctrine\Common\Cache\CacheProvider
     */
    abstract function setupCacheProvider();

    /**
     *
     * @see \Chamilo\Libraries\Cache\CacheServiceInterface::clearCache()
     */
    public function clearCache()
    {
        return $this->getCacheProvider()->deleteAll();
    }

    public function clearCacheForIdentifiers($identifiers)
    {
        foreach ($identifiers as $identifier)
        {
            if (! $this->getCacheProvider()->delete($identifier))
            {
                return false;
            }
        }

        return true;
    }

    /**
     *
     * @see \Chamilo\Libraries\Cache\CacheServiceInterface::getCache()
     */
    public function getCacheForIdentifier($identifier)
    {
        if (! $this->getCacheProvider()->contains($identifier))
        {
            if (! $this->fillCacheForIdentifier($identifier))
            {
                throw new \Exception('CacheError');
            }
        }

        return $this->getCacheProvider()->fetch($identifier);
    }
}