<?php
namespace Chamilo\Libraries\Storage\Query;

use Chamilo\Libraries\Architecture\Interfaces\Hashable;

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
    use \Chamilo\Libraries\Architecture\Traits\HashableTrait;

    /**
     * List of ConditionVariables to group by
     *
     * @var \Chamilo\Libraries\Storage\Query\Variable\ConditionVariable[]
     */
    private $group_by;

    /**
     *
     * @param \Chamilo\Libraries\Storage\Query\Variable\ConditionVariable[] $groupBy
     */
    public function __construct($groupBy = array())
    {
        $this->group_by = (is_array($groupBy) ? $groupBy : func_get_args());
    }

    /**
     *
     * @return \Chamilo\Libraries\Storage\Query\Variable\ConditionVariable[]
     */
    public function get()
    {
        return $this->group_by;
    }

    /**
     * Adds a group by
     *
     * @param \Chamilo\Libraries\Storage\Query\Variable\ConditionVariable $groupBy
     */
    public function add($groupBy)
    {
        $this->group_by[] = $groupBy;
    }

    /**
     *
     * @see \Chamilo\Libraries\Architecture\Interfaces\Hashable::getHashParts()
     */
    public function getHashParts()
    {
        $hashes = array();

        foreach ($this->get() as $group_by)
        {
            $hashes[] = $group_by->getHashParts();
        }

        sort($hashes);

        return $hashes;
    }
}
