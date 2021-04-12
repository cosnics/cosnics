<?php
namespace Chamilo\Libraries\Storage\Query;

use Chamilo\Libraries\Architecture\Interfaces\Hashable;
use Chamilo\Libraries\Architecture\Traits\HashableTrait;

/**
 * Describes the group by functionality of a query.
 * Uses ConditionVariable to define the group_by's
 *
 * @package Chamilo\Libraries\Storage\Query
 * @author Sven Vanpoucke <sven.vanpoucke@hogent.be>
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class GroupBy implements Hashable
{
    use HashableTrait;

    /**
     * List of ConditionVariables to group by
     *
     * @var \Chamilo\Libraries\Storage\Query\Variable\ConditionVariable[]
     */
    private $conditionVariables;

    /**
     *
     * @param \Chamilo\Libraries\Storage\Query\Variable\ConditionVariable[] $conditionVariables
     */
    public function __construct($conditionVariables = array())
    {
        $this->conditionVariables = (is_array($conditionVariables) ? $conditionVariables : func_get_args());
    }

    /**
     *
     * @return \Chamilo\Libraries\Storage\Query\Variable\ConditionVariable[]
     */
    public function get()
    {
        return $this->conditionVariables;
    }

    /**
     * Adds a group by
     *
     * @param \Chamilo\Libraries\Storage\Query\Variable\ConditionVariable $groupBy
     */
    public function add($groupBy)
    {
        $this->conditionVariables[] = $groupBy;
    }

    /**
     *
     * @see \Chamilo\Libraries\Architecture\Interfaces\Hashable::getHashParts()
     */
    public function getHashParts(): array
    {
        $hashes = array();

        foreach ($this->get() as $conditionVariable)
        {
            $hashes[] = $conditionVariable->getHashParts();
        }

        sort($hashes);

        return $hashes;
    }
}
