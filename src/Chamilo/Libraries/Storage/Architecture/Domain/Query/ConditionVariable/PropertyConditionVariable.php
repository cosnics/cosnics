<?php
namespace Chamilo\Libraries\Storage\Architecture\Domain\Query\ConditionVariable;

use Chamilo\Libraries\Storage\Architecture\Interface\ConditionVariableInterface;
use Chamilo\Libraries\Storage\Service\ConditionVariable\PropertyConditionVariableTranslator;

/**
 * @package Chamilo\Libraries\Storage\Architecture\Domain\Query\ConditionVariable
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Sven Vanpoucke <sven.vanpoucke@hogent.be> - Refactoring to extension of PropertiesConditionVariable
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class PropertyConditionVariable extends PropertiesConditionVariable implements ConditionVariableInterface
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

    public function setAlias(string $alias): static
    {
        $this->alias = $alias;

        return $this;
    }

    /**
     * @return class-string<\Chamilo\Libraries\Storage\Service\ConditionVariable\PropertyConditionVariableTranslator>
     */
    public function getConditionVariableTranslatorClass(): string
    {
        return PropertyConditionVariableTranslator::class;
    }

    /**
     *
     * @see \Chamilo\Libraries\Storage\Architecture\Domain\Query\ConditionVariable\PropertiesConditionVariable::getHashParts()
     */
    public function getHashParts(): array
    {
        return [
            static::class,
            $this->getDataClassName(),
            $this->getPropertyName(),
            $this->getAlias()
        ];
    }

    public function getPropertyName(): string
    {
        return $this->propertyName;
    }

    public function setPropertyName(string $propertyName): static
    {
        $this->propertyName = $propertyName;

        return $this;
    }
}
