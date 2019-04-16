<?php
namespace Chamilo\Libraries\Storage\Cache;

use Chamilo\Libraries\Storage\Query\Condition\Condition;

/**
 *
 * @package Chamilo\Libraries\Storage\Cache
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 * @deprecated Use ConditionPartCache now
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

    public function __construct()
    {
        $this->cache = array();
    }

    /**
     * Get an instance of the ConditionCache
     *
     * @return \Chamilo\Libraries\Storage\Cache\ConditionCache
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
     * Get a translated condition from the cache
     *
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     * @return string
     */
    public function get(Condition $condition)
    {
        if ($this->exists($condition))
        {
            return $this->cache[$condition->hash()];
        }
        else

        {
            return false;
        }
    }

    /**
     * Returns whether a Condition object exists in the cache
     *
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     * @return boolean
     */
    public function exists(Condition $condition)
    {
        if (isset($this->cache[$condition->hash()]))
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * Set the cache value for a specific Condition
     *
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     * @param string $value
     */
    public function set($condition, $value)
    {
        $this->cache[$condition->hash()] = $value;
    }

    public static function reset()
    {
        $instance = self::getInstance();
        $instance->cache = array();
    }
}
