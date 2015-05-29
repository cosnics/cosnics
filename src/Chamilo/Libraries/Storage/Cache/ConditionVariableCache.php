<?php
namespace Chamilo\Libraries\Storage\Cache;

use Chamilo\Libraries\Storage\Query\Variable\ConditionVariable;

/**
 *
 * @package Chamilo\Libraries\Storage\Cache
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class ConditionVariableCache
{

    /**
     *
     * @var \Chamilo\Libraries\Storage\Cache\ConditionVariableCache
     */
    private static $instance;

    /**
     * The cache
     *
     * @var mixed[][]
     */
    private $cache;

    /**
     * Get an instance of the DataClassCache
     *
     * @return \Chamilo\Libraries\Storage\Cache\ConditionVariableCache
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
     * Get a translated condition_variable from the cache
     *
     * @param \Chamilo\Libraries\Storage\Query\Variable\ConditionVariable $condition_variable
     * @return string boolean
     */
    public static function get(ConditionVariable $condition_variable)
    {
        $instance = self :: get_instance();

        if (self :: exists($condition_variable))
        {
            return $instance->cache[$condition_variable->hash()];
        }
        else

        {
            return false;
        }
    }

    /**
     * Returns whether a condition_variable object exists in the cache
     *
     * @param \Chamilo\Libraries\Storage\Query\Variable\ConditionVariable $condition_variable
     * @return boolean
     */
    public static function exists(ConditionVariable $condition_variable)
    {
        $instance = self :: get_instance();

        if (isset($instance->cache[$condition_variable->hash()]))
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * Set the cache value for a specific condition_variable
     *
     * @param string $class
     * @param string $hash
     * @param mixed $value
     */
    public static function set_cache($condition_variable, $value)
    {
        $instance = self :: get_instance();
        $instance->cache[$condition_variable->hash()] = $value;
    }

    public static function reset()
    {
        $instance = self :: get_instance();
        $instance->cache = array();
    }
}
