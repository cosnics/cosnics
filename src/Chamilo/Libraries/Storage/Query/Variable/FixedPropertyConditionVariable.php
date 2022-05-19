<?php
namespace Chamilo\Libraries\Storage\Query\Variable;

/**
 * A ConditionVariable that describes a regular DataClass property with a fixed alias
 *
 * @package Chamilo\Libraries\Storage\Query\Variable
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class FixedPropertyConditionVariable extends PropertyConditionVariable
{
    private string $alias;

    public function __construct(string $dataClassName, string $propertyName, string $alias)
    {
        parent::__construct($dataClassName, $propertyName);
        $this->alias = $alias;
    }

    public function getAlias(): string
    {
        return $this->alias;
    }

    public function setAlias(string $alias): FixedPropertyConditionVariable
    {
        $this->alias = $alias;

        return $this;
    }

    /**
     *
     * @see \Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable::getHashParts()
     */
    public function getHashParts(): array
    {
        $hashParts = parent::getHashParts();

        $hashParts[] = $this->getAlias();

        return $hashParts;
    }
}
