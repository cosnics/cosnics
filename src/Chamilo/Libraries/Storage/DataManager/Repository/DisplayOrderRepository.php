<?php
namespace Chamilo\Libraries\Storage\DataManager\Repository;

use Chamilo\Libraries\Storage\DataClass\CompositeDataClass;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\DataClass\Interfaces\DataClassDisplayOrderSupport;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\RecordRetrieveParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\ComparisonCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\NotCondition;
use Chamilo\Libraries\Storage\Query\RetrieveProperties;
use Chamilo\Libraries\Storage\Query\UpdateProperties;
use Chamilo\Libraries\Storage\Query\UpdateProperty;
use Chamilo\Libraries\Storage\Query\Variable\OperationConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * @package Chamilo\Libraries\Storage\DataManager\Repository
 *
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class DisplayOrderRepository
{
    private DataClassRepository $dataClassRepository;

    public function __construct(DataClassRepository $dataClassRepository)
    {
        $this->dataClassRepository = $dataClassRepository;
    }

    public function addDisplayOrderToContext(DataClassDisplayOrderSupport $dataClass): bool
    {
        $conditions = [];

        $displayOrderCondition = $this->getDisplayOrderCondition($dataClass);

        if ($displayOrderCondition instanceof AndCondition)
        {
            $conditions[] = $displayOrderCondition;
        }

        $displayOrder = $dataClass->getDefaultProperty($dataClass->getDisplayOrderPropertyName());

        $conditions[] = $this->getDisplayOrderUpdateComparisonCondition(
            $dataClass, ComparisonCondition::GREATER_THAN_OR_EQUAL, $displayOrder
        );

        return $this->getDataClassRepository()->updates(
            $this->determinePropertyDataClassName($dataClass),
            $this->getDisplayOrderUpdateDataClassProperties($dataClass, 1), new AndCondition($conditions)
        );
    }

    public function countOtherDisplayOrdersInContext(DataClassDisplayOrderSupport $dataClass): int
    {
        $conditions = [];

        $displayOrderCondition = $this->getDisplayOrderCondition($dataClass);

        if ($displayOrderCondition instanceof AndCondition)
        {
            $conditions[] = $displayOrderCondition;
        }

        if ($dataClass->isIdentified())
        {
            $conditions[] = new NotCondition(
                new EqualityCondition(
                    new PropertyConditionVariable(
                        $this->determinePropertyDataClassName($dataClass), DataClass::PROPERTY_ID
                    ), new StaticConditionVariable($dataClass->getId())
                )
            );
        }

        if (count($conditions))
        {
            $condition = new AndCondition($conditions);
        }
        else
        {
            $condition = null;
        }

        return $this->getDataClassRepository()->count(
            $this->determinePropertyDataClassName($dataClass), new DataClassCountParameters($condition)
        );
    }

    /**
     * @param string[] $contextProperties
     */
    public function deleteDisplayOrderFromContext(
        DataClassDisplayOrderSupport $dataClass, array $contextProperties, int $displayOrder
    ): bool
    {
        $conditions = [];

        $displayOrderCondition = $this->getDisplayOrderConditionForContextProperties($dataClass, $contextProperties);

        if ($displayOrderCondition instanceof AndCondition)
        {
            $conditions[] = $displayOrderCondition;
        }

        $conditions[] = $this->getDisplayOrderUpdateComparisonCondition(
            $dataClass, ComparisonCondition::GREATER_THAN, $displayOrder
        );

        return $this->getDataClassRepository()->updates(
            $this->determinePropertyDataClassName($dataClass),
            $this->getDisplayOrderUpdateDataClassProperties($dataClass, - 1), new AndCondition($conditions)
        );
    }

    protected function determinePropertyDataClassName(DataClassDisplayOrderSupport $dataClass): string
    {
        if ($dataClass instanceof CompositeDataClass)
        {
            return get_parent_class($dataClass);
        }
        else
        {
            return get_class($dataClass);
        }
    }

    /**
     * @return string[]
     */
    public function findDisplayOrderPropertiesRecord(DataClassDisplayOrderSupport $dataClass): array
    {
        $dataClassName = $this->determinePropertyDataClassName($dataClass);

        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                $dataClassName, DataClass::PROPERTY_ID
            ), new StaticConditionVariable($dataClass->getId())
        );

        $parameters = new RecordRetrieveParameters($this->getDisplayOrderDataClassProperties($dataClass), $condition);

        return $this->getDataClassRepository()->record($dataClassName, $parameters);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function findNextDisplayOrderValue(DataClassDisplayOrderSupport $dataClass): int
    {
        return $this->getDataClassRepository()->retrieveNextValue(
            $this->determinePropertyDataClassName($dataClass), $dataClass->getDisplayOrderPropertyName(),
            $this->getDisplayOrderCondition($dataClass)
        );
    }

    public function getDataClassRepository(): DataClassRepository
    {
        return $this->dataClassRepository;
    }

    /**
     * @param \Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository $dataClassRepository
     */
    public function setDataClassRepository(DataClassRepository $dataClassRepository): void
    {
        $this->dataClassRepository = $dataClassRepository;
    }

    protected function getDisplayOrderCondition(DataClassDisplayOrderSupport $dataClass): ?AndCondition
    {
        $displayOrderContextProperties = array_intersect_key(
            $dataClass->getDefaultProperties(), array_flip($dataClass->getDisplayOrderContextPropertyNames())
        );

        return $this->getDisplayOrderConditionForContextProperties(
            $dataClass, $displayOrderContextProperties
        );
    }

    protected function getDisplayOrderConditionForContextProperties(
        DataClassDisplayOrderSupport $dataClass, array $contextProperties
    ): ?AndCondition
    {
        $conditions = [];

        foreach ($contextProperties as $propertyName => $propertyValue)
        {
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(
                    $this->determinePropertyDataClassName($dataClass), $propertyName
                ), new StaticConditionVariable($propertyValue)
            );
        }

        return (count($conditions) > 0) ? new AndCondition($conditions) : null;
    }

    protected function getDisplayOrderDataClassProperties(DataClassDisplayOrderSupport $dataClass): RetrieveProperties
    {
        $retrieveProperties = new RetrieveProperties();

        $retrieveProperties->add($this->getDisplayOrderPropertyConditionVariable($dataClass));

        foreach ($dataClass->getDisplayOrderContextPropertyNames() as $propertyName)
        {
            $retrieveProperties->add(
                new PropertyConditionVariable(
                    $this->determinePropertyDataClassName($dataClass), $propertyName
                )
            );
        }

        return $retrieveProperties;
    }

    protected function getDisplayOrderPropertyConditionVariable(DataClassDisplayOrderSupport $dataClass
    ): PropertyConditionVariable
    {
        return new PropertyConditionVariable(
            $this->determinePropertyDataClassName($dataClass), $dataClass->getDisplayOrderPropertyName()
        );
    }

    protected function getDisplayOrderUpdateComparisonCondition(
        DataClassDisplayOrderSupport $dataClass, int $operator, int $displayOrder
    ): ComparisonCondition
    {
        return new ComparisonCondition(
            $this->getDisplayOrderPropertyConditionVariable($dataClass), $operator,
            new StaticConditionVariable($displayOrder)
        );
    }

    protected function getDisplayOrderUpdateDataClassProperties(
        DataClassDisplayOrderSupport $dataClass, int $additionValue
    ): UpdateProperties
    {
        $displayOrderPropertyConditionVariable = $this->getDisplayOrderPropertyConditionVariable($dataClass);

        $updateVariable = new OperationConditionVariable(
            $displayOrderPropertyConditionVariable, OperationConditionVariable::ADDITION,
            new StaticConditionVariable($additionValue)
        );

        return new UpdateProperties([new UpdateProperty($displayOrderPropertyConditionVariable, $updateVariable)]);
    }
}