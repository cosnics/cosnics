<?php
namespace Chamilo\Libraries\Storage\Query;

use Chamilo\Libraries\Architecture\Interfaces\Hashable;
use Chamilo\Libraries\Architecture\Traits\HashableTrait;
use Chamilo\Libraries\Storage\Query\Variable\ConditionVariable;

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
     * @var \Chamilo\Libraries\Storage\Query\Variable\ConditionVariable[]
     */
    private array $conditionVariables;

    /**
     *
     * @param \Chamilo\Libraries\Storage\Query\Variable\ConditionVariable[] $conditionVariables
     */
    public function __construct(array $conditionVariables = [])
    {
        $this->conditionVariables = $conditionVariables;
    }

    public function addConditionVariable(ConditionVariable $conditionVariable)
    {
        $this->conditionVariables[] = $conditionVariable;
    }

    /**
     *
     * @return \Chamilo\Libraries\Storage\Query\Variable\ConditionVariable[]
     */
    public function getConditionVariables(): array
    {
        return $this->conditionVariables;
    }

    /**
     *
     * @see \Chamilo\Libraries\Architecture\Interfaces\Hashable::getHashParts()
     */
    public function getHashParts(): array
    {
        $hashes = [];

        foreach ($this->getConditionVariables() as $conditionVariable)
        {
            $hashes[] = $conditionVariable->getHashParts();
        }

        sort($hashes);

        return $hashes;
    }
}
