<?php
namespace Chamilo\Libraries\Storage\Cache;

use Chamilo\Libraries\Storage\Query\Condition\Condition;

/**
 *
 * @package Chamilo\Libraries\Storage\Cache
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class ConditionCache
{

    /**
     * The instance of the ConditionCache
     *
     * @var \Chamilo\Libraries\Storage\Cache\ConditionCache
     */
    private static $instance;

    /**
     * The cache
     *
     * @var string[][]
     */
    private $cache;

    /**
     * Get an instance of the ConditionCache
     *
     * @return \Chamilo\Libraries\Storage\Cache\ConditionCache
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
     * Get a translated condition from the cache
     *
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     * @return boolean
     */
    public static function get(Condition $condition)
    {
        $instance = self :: get_instance();

        if (self :: exists($condition))
        {
            return $instance->cache[$condition->hash()];
        }
        else

        {
            return false;
        }
    }

    /**
     * Returns whether a condition object exists in the cache
     *
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     * @return boolean
     */
    public static function exists(Condition $condition)
    {
        $instance = self :: get_instance();

        if (isset($instance->cache[$condition->hash()]))
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * Set the cache value for a specific condition
     *
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     * @param mixed $value
     */
    public static function set_cache($condition, $value)
    {
        $instance = self :: get_instance();
        $instance->cache[$condition->hash()] = $value;
    }

    public static function reset()
    {
        $instance = self :: get_instance();
        $instance->cache = array();
    }
}
