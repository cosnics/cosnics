<?php
namespace Chamilo\Libraries\Storage\Query\Variable;

/**
 * A ConditionVariable that describes all the properties of a DataClass
 *
 * @package Chamilo\Libraries\Storage\Query\Variable
 * @author Sven Vanpoucke <sven.vanpoucke@hogent.be>
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class PropertiesConditionVariable extends ConditionVariable
{

    private string $dataClassName;

    public function __construct(string $dataClassName)
    {
        $this->dataClassName = $dataClassName;
    }

    public function getDataClassName(): string
    {
        return $this->dataClassName;
    }

    public function setDataClassName(string $dataClassName): PropertiesConditionVariable
    {
        $this->dataClassName = $dataClassName;

        return $this;
    }

    public function getHashParts(): array
    {
        $hashParts = ConditionVariable::getHashParts();

        $hashParts[] = $this->getDataClassName();

        return $hashParts;
    }
}
