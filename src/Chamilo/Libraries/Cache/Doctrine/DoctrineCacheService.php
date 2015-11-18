<?php
namespace Chamilo\Libraries\Cache\Doctrine;

use Chamilo\Libraries\Cache\IdentifiableCacheService;
use Chamilo\Libraries\File\Path;

/**
 *
 * @package Chamilo\Libraries\Cache\Doctrine
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class DoctrineCacheService extends IdentifiableCacheService
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
    public function clear()
    {
        return $this->getCacheProvider()->flushAll();
    }

    /**
     *
     * @see \Chamilo\Libraries\Cache\IdentifiableCacheService::clearForIdentifier()
     */
    public function clearForIdentifier($identifier)
    {
        return $this->getCacheProvider()->delete((string) $identifier);
    }

    /**
     *
     * @param \Chamilo\Libraries\Cache\ParameterBag|string $identifier
     * @throws \Exception
     * @return mixed
     */
    public function getForIdentifier($identifier)
    {
        if (! $this->getCacheProvider()->contains((string) $identifier))
        {
            if (! $this->warmUpForIdentifier($identifier))
            {
                throw new \Exception('CacheError');
            }
        }

        return $this->getCacheProvider()->fetch((string) $identifier);
    }
}