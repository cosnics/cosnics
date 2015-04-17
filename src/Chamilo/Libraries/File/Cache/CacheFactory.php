<?php
namespace Chamilo\Libraries\File\Cache;

/**
 *
 * @package Chamilo\Libraries\File\Cache
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class CacheFactory
{

    /**
     *
     * @var string
     */
    private $cacheStrategy;

    /**
     *
     * @var string
     */
    private $context;

    /**
     *
     * @var string
     */
    private $key;

    /**
     *
     * @param string $cacheStrategy
     * @param string $context
     * @param string $key
     */
    public function __construct($cacheStrategy, $context, $key)
    {
        $this->cacheStrategy = $cacheStrategy;
        $this->context = $context;
        $this->key = $key;
    }

    /**
     *
     * @return string
     */
    public function getCacheStrategy()
    {
        return $this->cacheStrategy;
    }

    /**
     *
     * @param string $cacheStrategy
     */
    public function setCacheStrategy($cacheStrategy)
    {
        $this->cacheStrategy = $cacheStrategy;
    }

    /**
     *
     * @return string
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     *
     * @param string $context
     */
    public function setContext($context)
    {
        $this->context = $context;
    }

    /**
     *
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     *
     * @param string $key
     */
    public function setKey($key)
    {
        $this->key = $key;
    }

    /**
     *
     * @return \Chamilo\Libraries\File\Cache\Cache
     */
    public function getCache()
    {
        $className = __NAMESPACE__ . '\\' . $this->getCacheStrategy() . 'Cache';
        return new $className($this->getContext(), $this->getKey());
    }
}