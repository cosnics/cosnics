<?php
namespace Chamilo\Libraries\Storage\Query\Variable;

/**
 * A case condition variable that describes a case in a select query
 *
 * @package Chamilo\Libraries\Storage\Query\Variable
 * @author Sven Vanpoucke <sven.vanpoucke@hogent.be>
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class CaseConditionVariable extends ConditionVariable
{

    private ?string $alias;

    /**
     * @var \Chamilo\Libraries\Storage\Query\Variable\CaseElementConditionVariable[]
     */
    private array $caseElementConditionVariables;

    /**
     * @param \Chamilo\Libraries\Storage\Query\Variable\CaseElementConditionVariable[] $caseElementConditionVariables
     */
    public function __construct(array $caseElementConditionVariables = [], ?string $alias = null)
    {
        $this->caseElementConditionVariables = $caseElementConditionVariables;
        $this->alias = $alias;
    }

    public function add(CaseElementConditionVariable $caseElementConditionVariable)
    {
        $this->caseElementConditionVariables[] = $caseElementConditionVariable;
    }

    /**
     * @return \Chamilo\Libraries\Storage\Query\Variable\CaseElementConditionVariable[]
     */
    public function get(): array
    {
        return $this->caseElementConditionVariables;
    }

    public function getAlias(): ?string
    {
        return $this->alias;
    }

    public function setAlias(string $alias): CaseConditionVariable
    {
        $this->alias = $alias;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getHashParts(): array
    {
        $hashParts = parent::getHashParts();

        foreach ($this->get() as $caseElementConditionVariable)
        {
            $hashParts[] = $caseElementConditionVariable->getHashParts();
        }

        sort($hashParts);

        $hashParts[] = $this->getAlias();

        return $hashParts;
    }

    /**
     * @param \Chamilo\Libraries\Storage\Query\Variable\CaseElementConditionVariable[] $caseElementConditionVariables
     */
    public function set(array $caseElementConditionVariables): CaseConditionVariable
    {
        $this->caseElementConditionVariables = $caseElementConditionVariables;

        return $this;
    }
}
