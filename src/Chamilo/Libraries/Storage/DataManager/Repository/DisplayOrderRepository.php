<?php
namespace Chamilo\Libraries\Storage\DataManager\Repository;

use Chamilo\Libraries\Storage\DataClass\CompositeDataClass;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\DataClass\Interfaces\DisplayOrderSupport;
use Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties;
use Chamilo\Libraries\Storage\DataClass\Property\DataClassProperty;
use Chamilo\Libraries\Storage\Parameters\RecordRetrieveParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\ComparisonCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\OperationConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * @package Chamilo\Libraries\Storage\DataManager\Repository
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
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
     * @param \Chamilo\Libraries\Storage\DataClass\Interfaces\DisplayOrderSupport $dataClass
     *
     * @return string
     */
    protected function determinePropertyDataClassName(DisplayOrderSupport $dataClass)
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
     * @param \Chamilo\Libraries\Storage\DataClass\Interfaces\DisplayOrderSupport $dataClass
     *
     * @return string[]
     * @throws \Exception
     */
    public function findDisplayOrderPropertiesRecord(DisplayOrderSupport $dataClass)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                $this->determinePropertyDataClassName($dataClass), DataClass::PROPERTY_ID
            ), new StaticConditionVariable($dataClass->getId())
        );

        $parameters = new RecordRetrieveParameters($this->getDisplayOrderDataClassProperties($dataClass), $condition);

        return $this->getDataClassRepository()->record(get_class($dataClass), $parameters);
    }

    /**
     * @param \Chamilo\Libraries\Storage\DataClass\Interfaces\DisplayOrderSupport $dataClass
     *
     * @return integer
     */
    public function findNextDisplayOrderValue(DisplayOrderSupport $dataClass)
    {
        return $this->getDataClassRepository()->retrieveNextValue(
            get_class($dataClass), $this->getDisplayOrderPropertyConditionVariable($dataClass),
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
     * @param \Chamilo\Libraries\Storage\DataClass\Interfaces\DisplayOrderSupport $dataClass
     *
     * @return \Chamilo\Libraries\Storage\Query\Condition\AndCondition
     */
    protected function getDisplayOrderCondition(DisplayOrderSupport $dataClass)
    {
        $conditions = array();

        foreach ($dataClass->getDisplayOrderContextPropertyNames() as $propertyName)
        {
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable($this->determinePropertyDataClassName($dataClass), $propertyName),
                new StaticConditionVariable($dataClass->getDefaultProperty($propertyName))
            );
        }

        return (count($conditions) > 0) ? new AndCondition($conditions) : null;
    }

    /**
     * @param \Chamilo\Libraries\Storage\DataClass\Interfaces\DisplayOrderSupport $dataClass
     *
     * @return \Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties
     */
    protected function getDisplayOrderDataClassProperties(DisplayOrderSupport $dataClass)
    {
        $dataClassProperties = new DataClassProperties();

        $dataClassProperties->add($this->getDisplayOrderPropertyConditionVariable($dataClass));

        foreach ($dataClass->getDisplayOrderContextPropertyNames() as $propertyName)
        {
            $dataClassProperties->add(
                new PropertyConditionVariable($this->determinePropertyDataClassName($dataClass), $propertyName)
            );
        }

        return $dataClassProperties;
    }

    /**
     * @param \Chamilo\Libraries\Storage\DataClass\Interfaces\DisplayOrderSupport $dataClass
     *
     * @return \Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable
     */
    protected function getDisplayOrderPropertyConditionVariable(DisplayOrderSupport $dataClass)
    {
        return new PropertyConditionVariable(
            $this->determinePropertyDataClassName($dataClass), $dataClass->getDisplayOrderPropertyName()
        );
    }

    /**
     * @param \Chamilo\Libraries\Storage\DataClass\Interfaces\DisplayOrderSupport $dataClass
     * @param integer $start
     * @param integer $end
     *
     * @return boolean
     */
    public function updateDisplayOrders(DisplayOrderSupport $dataClass, int $start = 1, int $end = null)
    {
        $dataClassName = $this->determinePropertyDataClassName($dataClass);

        if ($start == $end)
        {
            return false;
        }

        $displayOrderPropertyConditionVariable = $this->getDisplayOrderPropertyConditionVariable($dataClass);

        $conditions = array();

        if (is_null($end))
        {
            $startOperator = ComparisonCondition::GREATER_THAN;
            $direction = - 1;
        }
        else
        {
            if ($start < $end)
            {
                $startOperator = ComparisonCondition::GREATER_THAN;
                $direction = - 1;
                $endOperator = ComparisonCondition::LESS_THAN_OR_EQUAL;
            }
            else
            {
                $startOperator = ComparisonCondition::LESS_THAN;
                $endOperator = ComparisonCondition::GREATER_THAN_OR_EQUAL;
                $direction = 1;
            }

            $conditions[] = new ComparisonCondition(
                $displayOrderPropertyConditionVariable, $endOperator, new StaticConditionVariable($end)
            );
        }

        $conditions[] = new ComparisonCondition(
            $displayOrderPropertyConditionVariable, $startOperator, new StaticConditionVariable($start)
        );

        $displayOrderCondition = $this->getDisplayOrderCondition($dataClass);

        if ($displayOrderCondition instanceof AndCondition)
        {
            $conditions[] = $displayOrderCondition;
        }

        $condition = new AndCondition($conditions);

        $updateVariable = new OperationConditionVariable(
            $displayOrderPropertyConditionVariable, OperationConditionVariable::ADDITION,
            new StaticConditionVariable($direction)
        );

        $properties = new DataClassProperties(
            array(new DataClassProperty($displayOrderPropertyConditionVariable, $updateVariable))
        );

        return $this->getDataClassRepository()->updates($dataClassName, $properties, $condition);
    }

}