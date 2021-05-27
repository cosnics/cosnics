<?php
namespace Chamilo\Libraries\Storage\DataManager\Repository;

use Chamilo\Libraries\Storage\DataClass\CompositeDataClass;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\DataClass\Interfaces\DataClassDisplayOrderSupport;
use Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties;
use Chamilo\Libraries\Storage\DataClass\Property\DataClassProperty;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\RecordRetrieveParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\ComparisonCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\NotCondition;
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
    /**
     * @var \Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository
     */
    private $dataClassRepository;

    /**
     * @param \Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository $dataClassRepository
     */
    public function __construct(DataClassRepository $dataClassRepository)
    {
        $this->dataClassRepository = $dataClassRepository;
    }

    /**
     * @param \Chamilo\Libraries\Storage\DataClass\Interfaces\DataClassDisplayOrderSupport $dataClass
     *
     * @return boolean
     * @throws \Exception
     */
    public function addDisplayOrderToContext(DataClassDisplayOrderSupport $dataClass)
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

    /**
     * @param \Chamilo\Libraries\Storage\DataClass\Interfaces\DataClassDisplayOrderSupport $dataClass
     *
     * @return integer
     * @throws \Exception
     */
    public function countOtherDisplayOrdersInContext(DataClassDisplayOrderSupport $dataClass)
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
     * @param \Chamilo\Libraries\Storage\DataClass\Interfaces\DataClassDisplayOrderSupport $dataClass
     * @param string[] $contextProperties
     * @param integer $displayOrder
     *
     * @return boolean
     * @throws \Exception
     */
    public function deleteDisplayOrderFromContext(
        DataClassDisplayOrderSupport $dataClass, array $contextProperties, int $displayOrder
    )
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

    /**
     * @param \Chamilo\Libraries\Storage\DataClass\Interfaces\DataClassDisplayOrderSupport $dataClass
     *
     * @return string
     */
    protected function determinePropertyDataClassName(DataClassDisplayOrderSupport $dataClass)
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
     * @param \Chamilo\Libraries\Storage\DataClass\Interfaces\DataClassDisplayOrderSupport $dataClass
     *
     * @return string[]
     * @throws \Exception
     */
    public function findDisplayOrderPropertiesRecord(DataClassDisplayOrderSupport $dataClass)
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
     * @param \Chamilo\Libraries\Storage\DataClass\Interfaces\DataClassDisplayOrderSupport $dataClass
     *
     * @return integer
     * @throws \Exception
     */
    public function findNextDisplayOrderValue(DataClassDisplayOrderSupport $dataClass)
    {
        return $this->getDataClassRepository()->retrieveNextValue(
            $this->determinePropertyDataClassName($dataClass), $dataClass->getDisplayOrderPropertyName(),
            $this->getDisplayOrderCondition($dataClass)
        );
    }

    /**
     * @return \Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository
     */
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

    /**
     * Returns the display order condition based on the display order context properties
     *
     * @param \Chamilo\Libraries\Storage\DataClass\Interfaces\DataClassDisplayOrderSupport $dataClass
     *
     * @return \Chamilo\Libraries\Storage\Query\Condition\AndCondition
     * @throws \Exception
     */
    protected function getDisplayOrderCondition(DataClassDisplayOrderSupport $dataClass)
    {
        $displayOrderContextProperties = array_intersect_key(
            $dataClass->getDefaultProperties(), array_flip($dataClass->getDisplayOrderContextPropertyNames())
        );

        return $this->getDisplayOrderConditionForContextProperties(
            $dataClass, $displayOrderContextProperties
        );
    }

    /**
     * @param \Chamilo\Libraries\Storage\DataClass\Interfaces\DataClassDisplayOrderSupport $dataClass
     * @param string[] $contextProperties
     *
     * @return \Chamilo\Libraries\Storage\Query\Condition\AndCondition
     * @throws \Exception
     */
    protected function getDisplayOrderConditionForContextProperties(
        DataClassDisplayOrderSupport $dataClass, array $contextProperties
    )
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

    /**
     * @param \Chamilo\Libraries\Storage\DataClass\Interfaces\DataClassDisplayOrderSupport $dataClass
     *
     * @return \Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties
     * @throws \Exception
     */
    protected function getDisplayOrderDataClassProperties(DataClassDisplayOrderSupport $dataClass)
    {
        $dataClassProperties = new DataClassProperties();

        $dataClassProperties->add($this->getDisplayOrderPropertyConditionVariable($dataClass));

        foreach ($dataClass->getDisplayOrderContextPropertyNames() as $propertyName)
        {
            $dataClassProperties->add(
                new PropertyConditionVariable(
                    $this->determinePropertyDataClassName($dataClass), $propertyName
                )
            );
        }

        return $dataClassProperties;
    }

    /**
     * @param \Chamilo\Libraries\Storage\DataClass\Interfaces\DataClassDisplayOrderSupport $dataClass
     *
     * @return \Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable
     * @throws \Exception
     */
    protected function getDisplayOrderPropertyConditionVariable(DataClassDisplayOrderSupport $dataClass)
    {
        return new PropertyConditionVariable(
            $this->determinePropertyDataClassName($dataClass), $dataClass->getDisplayOrderPropertyName()
        );
    }

    /**
     * @param \Chamilo\Libraries\Storage\DataClass\Interfaces\DataClassDisplayOrderSupport $dataClass
     * @param integer $operator
     * @param integer $displayOrder
     *
     * @return \Chamilo\Libraries\Storage\Query\Condition\ComparisonCondition
     * @throws \Exception
     */
    protected function getDisplayOrderUpdateComparisonCondition(
        DataClassDisplayOrderSupport $dataClass, int $operator, int $displayOrder
    )
    {
        return new ComparisonCondition(
            $this->getDisplayOrderPropertyConditionVariable($dataClass), $operator,
            new StaticConditionVariable($displayOrder)
        );
    }

    /**
     * @param \Chamilo\Libraries\Storage\DataClass\Interfaces\DataClassDisplayOrderSupport $dataClass
     * @param integer $additionValue
     *
     * @return \Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties
     * @throws \Exception
     */
    protected function getDisplayOrderUpdateDataClassProperties(
        DataClassDisplayOrderSupport $dataClass, int $additionValue
    )
    {
        $displayOrderPropertyConditionVariable = $this->getDisplayOrderPropertyConditionVariable($dataClass);

        $updateVariable = new OperationConditionVariable(
            $displayOrderPropertyConditionVariable, OperationConditionVariable::ADDITION,
            new StaticConditionVariable($additionValue)
        );

        return new DataClassProperties(
            array(new DataClassProperty($displayOrderPropertyConditionVariable, $updateVariable))
        );
    }
}