<?php
namespace Chamilo\Libraries\Storage\Query;

use Chamilo\Libraries\Architecture\Interfaces\Hashable;
use Chamilo\Libraries\Architecture\Traits\HashableTrait;
use Chamilo\Libraries\Storage\Query\Variable\ConditionVariable;

/**
 * Describes the order by functionality of a query.
 * Uses ConditionVariable to define the property
 *
 * @package Chamilo\Libraries\Storage\Query
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class OrderProperty implements Hashable
{
    use HashableTrait;

    private ConditionVariable $conditionVariable;

    private int $direction;

    public function __construct(ConditionVariable $conditionVariable, ?int $direction = SORT_ASC)
    {
        $this->conditionVariable = $conditionVariable;
        $this->direction = $direction;
    }

    public function getConditionVariable(): ConditionVariable
    {
        return $this->conditionVariable;
    }

    public function setConditionVariable(ConditionVariable $conditionVariable): OrderProperty
    {
        $this->conditionVariable = $conditionVariable;

        return $this;
    }

    public function getDirection(): int
    {
        return $this->direction;
    }

    public function setDirection(int $direction): OrderProperty
    {
        $this->direction = $direction;

        return $this;
    }

    public function getHashParts(): array
    {
        return [$this->getConditionVariable()->getHashParts(), $this->getDirection()];
    }
}
