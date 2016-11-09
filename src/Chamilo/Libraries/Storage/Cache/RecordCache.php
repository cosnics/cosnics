<?php
namespace Chamilo\Libraries\Storage\Cache;

use Chamilo\Libraries\Storage\Parameters\DataClassParameters;

/**
 *
 * @package Chamilo\Libraries\Storage\Cache
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 * @deprecated Use DataClassRepositoryCache now
 */
class RecordCache
{

    /**
     * The instance of the RecordCache
     *
     * @var \Chamilo\Libraries\Storage\Cache\RecordCache
     */
    private static $instance;

    /**
     * The cache
     *
     * @var mixed[][]
     */
    private $cache;

    /**
     * Get an instance of the RecordCache
     *
     * @return \Chamilo\Libraries\Storage\Cache\RecordCache
     */
    public static function getInstance()
    {
        if (! isset(self::$instance))
        {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Get a DataClass object from the cache
     *
     * @param string $class
     * @param \Chamilo\Libraries\Storage\Parameters\DataClassParameters $parameters
     * @return boolean
     */
    public static function get($class, DataClassParameters $parameters)
    {
        $instance = self::getInstance();

        if (self::exists($class, $parameters))
        {
            return $instance->cache[$class][$parameters->hash()];
        }
        else

        {
            return false;
        }
    }

    /**
     * Returns whether a DataClass object exists in the cache
     *
     * @param string $class
     * @param \Chamilo\Libraries\Storage\Parameters\DataClassParameters $parameters
     * @return boolean
     */
    public static function exists($class, DataClassParameters $parameters)
    {
        $instance = self::getInstance();
        $hash = $parameters->hash();

        if (isset($instance->cache[$class][$hash]))
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * Clear the cache for a specific DataClass type
     *
     * @param string $class
     * @return boolean
     */
    public static function truncate($class)
    {
        $instance = self::getInstance();

        if (isset($instance->cache[$class]))
        {
            unset($instance->cache[$class]);
        }

        return true;
    }

    /**
     * Clear the cache for a set of specific DataClass types
     *
     * @param string[] $classes
     * @return boolean
     */
    public static function truncates($classes = array())
    {
        foreach ($classes as $class)
        {
            if (! self::truncate($class))
            {
                return false;
            }
        }

        return true;
    }

    /**
     * Set the cache value for a specific DataClass object type, hash
     *
     * @param string $class
     * @param string $hash
     * @param mixed $value
     */
    public static function set_cache($class, $hash, $value)
    {
        $instance = self::getInstance();
        $instance->cache[$class][$hash] = $value;
    }

    public static function reset()
    {
        $instance = self::getInstance();
        $instance->cache = array();
    }
}
