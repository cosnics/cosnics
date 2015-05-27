<?php
namespace Chamilo\Libraries\Storage\Cache;

use Chamilo\Libraries\Storage\Parameters\DataClassParameters;

/**
 *
 * @package Chamilo\Libraries\Storage\Cache
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
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
    public static function get_instance()
    {
        if (! isset(self :: $instance))
        {
            self :: $instance = new self();
        }
        return self :: $instance;
    }

    /**
     * Get a DataClass object from the cache
     *
     * @param string $class
     * @param \Chamilo\Libraries\Storage\Parameters\DataClassParameters $parameters
     * @return boolean
     */
    public static function get(DataClassParameters $parameters)
    {
        $instance = self :: get_instance();

        if (self :: exists($parameters))
        {
            return $instance->cache[$parameters->hash()];
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
    public static function exists(DataClassParameters $parameters)
    {
        $instance = self :: get_instance();

        if (isset($instance->cache[$parameters->hash()]))
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
    public static function truncate()
    {
        $instance = self :: get_instance();

        if (isset($instance->cache))
        {
            unset($instance->cache);
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
    public static function set_cache($hash, $value)
    {
        $instance = self :: get_instance();
        $instance->cache[$hash] = $value;
    }

    public static function reset()
    {
        $instance = self :: get_instance();
        $instance->cache = array();
    }
}
