<?php
namespace Chamilo\Libraries\Storage\Cache;

use Chamilo\Libraries\Storage\Query\Variable\ConditionVariable;

/**
 *
 * @package Chamilo\Libraries\Storage\Cache
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 * @deprecated Use ConditionPartCache now
 */
class ConditionVariableCache
{

    /**
     * The instance of the ConditionVariableCache
     *
     * @var \Chamilo\Libraries\Storage\Cache\ConditionVariableCache
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
     * Returns whether a ConditionVariable object exists in the cache
     *
     * @param \Chamilo\Libraries\Storage\Query\Variable\ConditionVariable $conditionVariable
     *
     * @return boolean
     */
    public function exists(ConditionVariable $conditionVariable)
    {
        if (isset($this->cache[$conditionVariable->hash()]))
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * Get a translated condition_variable from the cache
     *
     * @param \Chamilo\Libraries\Storage\Query\Variable\ConditionVariable $conditionVariable
     *
     * @return string
     */
    public function get(ConditionVariable $conditionVariable)
    {
        if ($this->exists($conditionVariable))
        {
            return $this->cache[$conditionVariable->hash()];
        }
        else

        {
            return false;
        }
    }

    /**
     * Get an instance of the ConditionVariableCache
     *
     * @return \Chamilo\Libraries\Storage\Cache\ConditionVariableCache
     */
    public static function getInstance()
    {
        if (!isset(self::$instance))
        {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public static function reset()
    {
        $instance = self::getInstance();
        $instance->cache = array();
    }

    /**
     * Set the cache value for a specific ConditionVariable
     *
     * @param \Chamilo\Libraries\Storage\Query\Variable\ConditionVariable $conditionVariable
     * @param string $value
     */
    public function set($conditionVariable, $value)
    {
        $this->cache[$conditionVariable->hash()] = $value;
    }
}
