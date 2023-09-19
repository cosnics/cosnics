<?php
namespace Chamilo\Libraries\Storage\Query;

use Chamilo\Libraries\Architecture\Interfaces\HashableInterface;
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
class GroupBy implements HashableInterface
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

    public function add(ConditionVariable $conditionVariable)
    {
        $this->conditionVariables[] = $conditionVariable;
    }

    /**
     *
     * @return \Chamilo\Libraries\Storage\Query\Variable\ConditionVariable[]
     */
    public function get(): array
    {
        return $this->conditionVariables;
    }

    /**
     * @return string[]
     */
    public function getHashParts(): array
    {
        $hashes = [];

        foreach ($this->get() as $conditionVariable)
        {
            $hashes[] = $conditionVariable->getHashParts();
        }

        sort($hashes);

        return $hashes;
    }
}
