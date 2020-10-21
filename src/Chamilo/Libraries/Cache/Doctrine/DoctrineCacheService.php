<?php
namespace Chamilo\Libraries\Cache\Doctrine;

use Chamilo\Libraries\Cache\IdentifiableCacheService;
use Chamilo\Libraries\File\ConfigurablePathBuilder;
use Exception;

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
     * @var \Chamilo\Libraries\File\ConfigurablePathBuilder
     */
    protected $configurablePathBuilder;

    /**
     *
     * @var \Doctrine\Common\Cache\CacheProvider
     */
    private $cacheProvider;

    /**
     * @param \Chamilo\Libraries\File\ConfigurablePathBuilder $configurablePathBuilder
     */
    public function __construct(ConfigurablePathBuilder $configurablePathBuilder)
    {
        $this->configurablePathBuilder = $configurablePathBuilder;
    }

    /**
     *
     * @see \Chamilo\Libraries\Cache\IdentifiableCacheService::clear()
     */
    public function clear()
    {
        return $this->getCacheProvider()->flushAll();
    }

    /**
     * @param \Chamilo\Libraries\Cache\ParameterBag|string $identifier
     *
     * @return bool
     */
    public function clearForIdentifier($identifier)
    {
        return $this->getCacheProvider()->delete((string) $identifier);
    }

    /**
     *
     * @return string
     */
    protected function getCachePath()
    {
        return $this->getConfigurablePathBuilder()->getCachePath($this->getCachePathNamespace());
    }

    /**
     *
     * @return string
     */
    abstract function getCachePathNamespace();

    /**
     *
     * @return \Doctrine\Common\Cache\CacheProvider
     */
    public function getCacheProvider()
    {
        if (!isset($this->cacheProvider))
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
     * @return \Chamilo\Libraries\File\ConfigurablePathBuilder
     */
    public function getConfigurablePathBuilder(): ConfigurablePathBuilder
    {
        return $this->configurablePathBuilder;
    }

    /**
     * @param \Chamilo\Libraries\File\ConfigurablePathBuilder $configurablePathBuilder
     *
     * @return DoctrineCacheService
     */
    public function setConfigurablePathBuilder(ConfigurablePathBuilder $configurablePathBuilder): DoctrineCacheService
    {
        $this->configurablePathBuilder = $configurablePathBuilder;

        return $this;
    }

    /**
     * @param \Chamilo\Libraries\Cache\ParameterBag|string $identifier
     *
     * @return false|mixed
     * @throws \Exception
     */
    public function getForIdentifier($identifier)
    {
        if (!$this->getCacheProvider()->contains((string) $identifier))
        {
            if (!$this->warmUpForIdentifier($identifier))
            {
                throw new Exception('CacheError');
            }
        }

        return $this->getCacheProvider()->fetch((string) $identifier);
    }

    /**
     *
     * @return \Doctrine\Common\Cache\CacheProvider
     */
    abstract function setupCacheProvider();
}