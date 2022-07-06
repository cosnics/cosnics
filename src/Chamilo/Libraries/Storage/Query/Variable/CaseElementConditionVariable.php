<?php
namespace Chamilo\Libraries\Storage\Query\Variable;

use Chamilo\Libraries\Storage\Query\Condition\Condition;

/**
 * A case element condition variable that describes a single element of a case in a select query
 *
 * @package Chamilo\Libraries\Storage\Query\Variable
 * @author Sven Vanpoucke <sven.vanpoucke@hogent.be>
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class CaseElementConditionVariable extends ConditionVariable
{

    /**
     * The condition used after the WHEN statement.
     * If empty the case element is an ELSE statement.
     */
    private ?Condition $condition;

    private ConditionVariable $statement;

    public function __construct(ConditionVariable $statement, ?Condition $condition = null)
    {
        $this->statement = $statement;
        $this->condition = $condition;
    }

    public function getCondition(): ?Condition
    {
        return $this->condition;
    }

    public function setCondition($condition): CaseElementConditionVariable
    {
        $this->condition = $condition;

        return $this;
    }

    public function getHashParts(): array
    {
        $hashParts = ConditionVariable::getHashParts();

        if ($this->getCondition() instanceof Condition)
        {
            $hashParts[] = $this->getCondition()->hash();
        }
        else
        {
            $hashParts[] = null;
        }

        $hashParts[] = $this->getStatement()->hash();

        return $hashParts;
    }

    public function getStatement(): ConditionVariable
    {
        return $this->statement;
    }

    public function setStatement(ConditionVariable $statement): CaseElementConditionVariable
    {
        $this->statement = $statement;

        return $this;
    }
}
