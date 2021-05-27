<?php
namespace Chamilo\Libraries\Storage\Cache;

use Chamilo\Libraries\Storage\Query\ConditionPart;

/**
 *
 * @package Chamilo\Libraries\Storage\Cache
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
class ConditionPartCache
{

    /**
     * The cache
     *
     * @var string[][]
     */
    private $cache;

    /**
     */
    public function __construct()
    {
        $this->cache = [];
    }

    /**
     * Returns whether a Condition object exists in the cache
     *
     * @param \Chamilo\Libraries\Storage\Query\ConditionPart $conditionPart
     *
     * @return boolean
     */
    public function exists(ConditionPart $conditionPart)
    {
        if (isset($this->cache[$conditionPart->hash()]))
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * Get a translated condition from the cache
     *
     * @param \Chamilo\Libraries\Storage\Query\ConditionPart $conditionPart
     *
     * @return string
     */
    public function get(ConditionPart $conditionPart)
    {
        if ($this->exists($conditionPart))
        {
            return $this->cache[$conditionPart->hash()];
        }
        else
        {
            return false;
        }
    }

    /**
     * Set the cache value for a specific Condition
     *
     * @param \Chamilo\Libraries\Storage\Query\ConditionPart $conditionPart
     * @param string $value
     */
    public function set($conditionPart, $value)
    {
        $this->cache[$conditionPart->hash()] = $value;
    }
}
