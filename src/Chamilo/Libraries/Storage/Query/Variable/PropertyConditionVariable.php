<?php
namespace Chamilo\Libraries\Storage\Query\Variable;

/**
 * A ConditionVariable that describes a regular DataClass property
 *
 * @package Chamilo\Libraries\Storage\Query\Variable
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Sven Vanpoucke <sven.vanpoucke@hogent.be> - Refactoring to extension of PropertiesConditionVariable
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class PropertyConditionVariable extends PropertiesConditionVariable
{

    private ?string $alias;

    private string $propertyName;

    public function __construct(string $dataClassName, string $propertyName, ?string $alias = null)
    {
        parent::__construct($dataClassName);

        $this->propertyName = $propertyName;
        $this->alias = $alias;
    }

    public function getAlias(): ?string
    {
        return $this->alias;
    }

    public function setAlias(string $alias): PropertyConditionVariable
    {
        $this->alias = $alias;

        return $this;
    }

    /**
     *
     * @see \Chamilo\Libraries\Storage\Query\Variable\PropertiesConditionVariable::getHashParts()
     */
    public function getHashParts(): array
    {
        $hashParts = ConditionVariable::getHashParts();

        $hashParts[] = $this->getDataClassName();
        $hashParts[] = $this->getPropertyName();
        $hashParts[] = $this->getAlias();

        return $hashParts;
    }

    public function getPropertyName(): string
    {
        return $this->propertyName;
    }

    public function setPropertyName(string $propertyName): PropertyConditionVariable
    {
        $this->propertyName = $propertyName;

        return $this;
    }
}
