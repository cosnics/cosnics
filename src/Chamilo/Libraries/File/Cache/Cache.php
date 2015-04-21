<?php
namespace Chamilo\Libraries\File\Cache;

/**
 *
 * @package Chamilo\Libraries\File\Cache
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Sven Vanpoucke <sven.vanpoucke@hogent.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class Cache
{
    const TYPE_SERIALIZE = 'Serialize';
    const TYPE_PHP = 'Php';

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
     * @param string $context
     * @param string $key
     */
    public function __construct($context, $key)
    {
        if (! is_string($context) || ! is_string($key))
        {
            throw new \InvalidArgumentException();
        }

        $this->context = $context;
        $this->key = $key;
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
     * @return mixed
     */
    abstract public function get();

    /**
     *
     * @param mixed $data
     */
    abstract public function set($data);

    abstract public function truncate();

    /**
     *
     * @return boolean
     */
    abstract public function isCacheSet();

    /**
     *
     * @throws CacheUnavailableException
     * @return boolean
     */
    public function verifyCache()
    {
        if (! $this->isCacheSet())
        {
            throw new CacheUnavailableException();
        }

        return true;
    }
}