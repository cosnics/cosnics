<?php
namespace Chamilo\Libraries\Storage\Repository;

use Chamilo\Libraries\Storage\Architecture\Domain\DataClass;
use Chamilo\Libraries\Storage\Architecture\Domain\Enum\ComparisonTypeEnum;
use Chamilo\Libraries\Storage\Architecture\Domain\Enum\OperationTypeEnum;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\Condition\ComparisonCondition;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\Condition\NotCondition;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\ConditionVariable\OperationConditionVariable;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\ConditionVariable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\ConditionVariable\StaticConditionVariable;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\RetrieveProperties;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\UpdateProperties;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\UpdateProperty;
use Chamilo\Libraries\Storage\Architecture\Domain\StorageParameters;
use Chamilo\Libraries\Storage\Architecture\Interface\DataClassDisplayOrderSupport;

/**
 * @package Chamilo\Libraries\Storage\Repository
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class DisplayOrderRepository
{
    public function __construct(protected DataClassRepository $dataClassRepository)
    {
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function addDisplayOrderToContext(DataClassDisplayOrderSupport $dataClass): bool
    {
        $conditions = [];

        $displayOrderCondition = $this->getDisplayOrderCondition($dataClass);

        if ($displayOrderCondition instanceof AndCondition) {
            $conditions[] = $displayOrderCondition;
        }

        $displayOrder = $dataClass->getDefaultProperty($dataClass->getDisplayOrderPropertyName());

        $conditions[] = $this->getDisplayOrderUpdateComparisonCondition(
            $dataClass, ComparisonTypeEnum::GREATER_THAN_OR_EQUAL, $displayOrder
        );

        return $this->dataClassRepository->updates(
            $this->determinePropertyDataClassName($dataClass),
            $this->getDisplayOrderUpdateDataClassProperties($dataClass, 1), new AndCondition($conditions)
        );
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function countOtherDisplayOrdersInContext(DataClassDisplayOrderSupport $dataClass): int
    {
        $conditions = [];

        $displayOrderCondition = $this->getDisplayOrderCondition($dataClass);

        if ($displayOrderCondition instanceof AndCondition) {
            $conditions[] = $displayOrderCondition;
        }

        if ($dataClass->isIdentified()) {
            $conditions[] = new NotCondition(
                new EqualityCondition(
                    new PropertyConditionVariable(
                        $this->determinePropertyDataClassName($dataClass), DataClass::PROPERTY_ID
                    ), new StaticConditionVariable($dataClass->getId())
                )
            );
        }

        if (count($conditions)) {
            $condition = new AndCondition($conditions);
        }
        else {
            $condition = null;
        }

        return $this->dataClassRepository->count(
            $this->determinePropertyDataClassName($dataClass), new StorageParameters(condition: $condition)
        );
    }

    /**
     * @param string[] $contextProperties
     *
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function deleteDisplayOrderFromContext(
        DataClassDisplayOrderSupport $dataClass, array $contextProperties, int $displayOrder
    ): bool
    {
        $conditions = [];

        $displayOrderCondition = $this->getDisplayOrderConditionForContextProperties($dataClass, $contextProperties);

        if ($displayOrderCondition instanceof AndCondition) {
            $conditions[] = $displayOrderCondition;
        }

        $conditions[] = $this->getDisplayOrderUpdateComparisonCondition(
            $dataClass, ComparisonTypeEnum::GREATER_THAN, $displayOrder
        );

        return $this->dataClassRepository->updates(
            $this->determinePropertyDataClassName($dataClass),
            $this->getDisplayOrderUpdateDataClassProperties($dataClass, - 1), new AndCondition($conditions)
        );
    }

    protected function determinePropertyDataClassName(DataClassDisplayOrderSupport $dataClass): string
    {
        return $dataClass::class;
    }

    /**
     * @return string[]
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     */
    public function findDisplayOrderPropertiesRecord(DataClassDisplayOrderSupport $dataClass): array
    {
        $dataClassName = $this->determinePropertyDataClassName($dataClass);

        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                $dataClassName, DataClass::PROPERTY_ID
            ), new StaticConditionVariable($dataClass->getId())
        );

        $parameters = new StorageParameters(
            condition: $condition, retrieveProperties: $this->getDisplayOrderDataClassProperties($dataClass)
        );

        return $this->dataClassRepository->record($dataClassName, $parameters);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     */
    public function findNextDisplayOrderValue(DataClassDisplayOrderSupport $dataClass): int
    {
        return $this->dataClassRepository->retrieveNextValue(
            $this->determinePropertyDataClassName($dataClass), $dataClass->getDisplayOrderPropertyName(),
            $this->getDisplayOrderCondition($dataClass)
        );
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

        foreach ($contextProperties as $propertyName => $propertyValue) {
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

        foreach ($dataClass->getDisplayOrderContextPropertyNames() as $propertyName) {
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
        DataClassDisplayOrderSupport $dataClass, ComparisonTypeEnum $operator, int $displayOrder
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
            $displayOrderPropertyConditionVariable, OperationTypeEnum::ADDITION,
            new StaticConditionVariable($additionValue)
        );

        return new UpdateProperties([new UpdateProperty($displayOrderPropertyConditionVariable, $updateVariable)]);
    }

    /**
     * @param \Chamilo\Libraries\Storage\Repository\DataClassRepository $dataClassRepository
     */
    public function setDataClassRepository(DataClassRepository $dataClassRepository): void
    {
        $this->dataClassRepository = $dataClassRepository;
    }
}