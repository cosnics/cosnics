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

    public function addCaseElementConditionVariable(CaseElementConditionVariable $caseElementConditionVariable)
    {
        $this->caseElementConditionVariables[] = $caseElementConditionVariable;
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
     * Get the case_elements
     *
     * @return \Chamilo\Libraries\Storage\Query\Variable\CaseElementConditionVariable[]
     */
    public function getCaseElementConditionVariables()
    {
        return $this->caseElementConditionVariables;
    }

    /**
     * @param \Chamilo\Libraries\Storage\Query\Variable\CaseElementConditionVariable[] $caseElementConditionVariables
     */
    public function setCaseElementConditionVariables(array $caseElementConditionVariables): CaseConditionVariable
    {
        $this->caseElementConditionVariables = $caseElementConditionVariables;

        return $this;
    }

    /**
     *
     * @see \Chamilo\Libraries\Storage\Query\ConditionPart::getHashParts()
     */
    public function getHashParts(): array
    {
        $hashParts = parent::getHashParts();

        foreach ($this->getCaseElementConditionVariables() as $case_element)
        {
            $hashParts[] = $case_element->getHashParts();
        }

        sort($hashParts);

        $hashParts[] = $this->getAlias();

        return $hashParts;
    }
}
