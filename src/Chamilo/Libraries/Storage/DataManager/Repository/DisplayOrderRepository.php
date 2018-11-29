<?php
namespace Chamilo\Libraries\Storage\DataManager\Repository;

use Chamilo\Libraries\Storage\DataClass\CompositeDataClass;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\DataClass\Interfaces\DataClassDisplayOrderSupport;
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
     * @param \Chamilo\Libraries\Storage\DataClass\Interfaces\DataClassDisplayOrderSupport $displayOrderDataClass
     *
     * @return boolean
     */
    public function addDisplayOrderToContext(DataClassDisplayOrderSupport $displayOrderDataClass)
    {
        $conditions = array();

        $displayOrderCondition = $this->getDisplayOrderCondition($displayOrderDataClass);

        if ($displayOrderCondition instanceof AndCondition)
        {
            $conditions[] = $displayOrderCondition;
        }

        $displayOrder =
            $displayOrderDataClass->getDefaultProperty($displayOrderDataClass->getDisplayOrderPropertyName());

        $conditions[] = $this->getDisplayOrderUpdateComparisonCondition(
            $displayOrderDataClass, ComparisonCondition::GREATER_THAN_OR_EQUAL, $displayOrder
        );

        return $this->getDataClassRepository()->updates(
            $this->determinePropertyDataClassName($displayOrderDataClass),
            $this->getDisplayOrderUpdateDataClassProperties($displayOrderDataClass, 1), new AndCondition($conditions)
        );
    }

    /**
     * @param \Chamilo\Libraries\Storage\DataClass\Interfaces\DataClassDisplayOrderSupport $displayOrderDataClass
     * @param string[] $contextProperties
     * @param integer $displayOrder
     *
     * @return boolean
     */
    public function deleteDisplayOrderFromContext(
        DataClassDisplayOrderSupport $displayOrderDataClass, array $contextProperties, int $displayOrder
    )
    {
        $conditions = array();

        $displayOrderCondition =
            $this->getDisplayOrderConditionForContextProperties($displayOrderDataClass, $contextProperties);

        if ($displayOrderCondition instanceof AndCondition)
        {
            $conditions[] = $displayOrderCondition;
        }

        $conditions[] = $this->getDisplayOrderUpdateComparisonCondition(
            $displayOrderDataClass, ComparisonCondition::GREATER_THAN, $displayOrder
        );

        return $this->getDataClassRepository()->updates(
            $this->determinePropertyDataClassName($displayOrderDataClass),
            $this->getDisplayOrderUpdateDataClassProperties($displayOrderDataClass, - 1), new AndCondition($conditions)
        );
    }

    /**
     * @param \Chamilo\Libraries\Storage\DataClass\Interfaces\DataClassDisplayOrderSupport $displayOrderDataClass
     *
     * @return string
     */
    protected function determinePropertyDataClassName(DataClassDisplayOrderSupport $displayOrderDataClass)
    {
        if ($displayOrderDataClass instanceof CompositeDataClass)
        {
            return get_parent_class($displayOrderDataClass);
        }
        else
        {
            return get_class($displayOrderDataClass);
        }
    }

    /**
     * @param \Chamilo\Libraries\Storage\DataClass\Interfaces\DataClassDisplayOrderSupport $displayOrderDataClass
     *
     * @return string[]
     * @throws \Exception
     */
    public function findDisplayOrderPropertiesRecord(DataClassDisplayOrderSupport $displayOrderDataClass)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                $this->determinePropertyDataClassName($displayOrderDataClass), DataClass::PROPERTY_ID
            ), new StaticConditionVariable($displayOrderDataClass->getId())
        );

        $parameters =
            new RecordRetrieveParameters($this->getDisplayOrderDataClassProperties($displayOrderDataClass), $condition);

        return $this->getDataClassRepository()->record(get_class($displayOrderDataClass), $parameters);
    }

    /**
     * @param \Chamilo\Libraries\Storage\DataClass\Interfaces\DataClassDisplayOrderSupport $displayOrderDataClass
     *
     * @return integer
     */
    public function findNextDisplayOrderValue(DataClassDisplayOrderSupport $displayOrderDataClass)
    {
        return $this->getDataClassRepository()->retrieveNextValue(
            get_class($displayOrderDataClass), $this->getDisplayOrderPropertyConditionVariable($displayOrderDataClass),
            $this->getDisplayOrderCondition($displayOrderDataClass)
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
     * @param \Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository $displayOrderDataClassRepository
     */
    public function setDataClassRepository(DataClassRepository $displayOrderDataClassRepository): void
    {
        $this->dataClassRepository = $displayOrderDataClassRepository;
    }

    /**
     * Returns the display order condition based on the display order context properties
     *
     * @param \Chamilo\Libraries\Storage\DataClass\Interfaces\DataClassDisplayOrderSupport $displayOrderDataClass
     *
     * @return \Chamilo\Libraries\Storage\Query\Condition\AndCondition
     */
    protected function getDisplayOrderCondition(DataClassDisplayOrderSupport $displayOrderDataClass)
    {
        $displayOrderContextProperties = array_intersect_key(
            $displayOrderDataClass->getDefaultProperties(),
            array_flip($displayOrderDataClass->getDisplayOrderContextPropertyNames())
        );

        return $this->getDisplayOrderConditionForContextProperties(
            $displayOrderDataClass, $displayOrderContextProperties
        );
    }

    /**
     * @param \Chamilo\Libraries\Storage\DataClass\Interfaces\DataClassDisplayOrderSupport $displayOrderDataClass
     * @param string[] $contextProperties
     *
     * @return \Chamilo\Libraries\Storage\Query\Condition\AndCondition
     */
    protected function getDisplayOrderConditionForContextProperties(
        DataClassDisplayOrderSupport $displayOrderDataClass, array $contextProperties
    )
    {
        $conditions = array();

        foreach ($contextProperties as $propertyName => $propertyValue)
        {
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(
                    $this->determinePropertyDataClassName($displayOrderDataClass), $propertyName
                ), new StaticConditionVariable($propertyValue)
            );
        }

        return (count($conditions) > 0) ? new AndCondition($conditions) : null;
    }

    /**
     * @param \Chamilo\Libraries\Storage\DataClass\Interfaces\DataClassDisplayOrderSupport $displayOrderDataClass
     *
     * @return \Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties
     */
    protected function getDisplayOrderDataClassProperties(DataClassDisplayOrderSupport $displayOrderDataClass)
    {
        $displayOrderDataClassProperties = new DataClassProperties();

        $displayOrderDataClassProperties->add($this->getDisplayOrderPropertyConditionVariable($displayOrderDataClass));

        foreach ($displayOrderDataClass->getDisplayOrderContextPropertyNames() as $propertyName)
        {
            $displayOrderDataClassProperties->add(
                new PropertyConditionVariable(
                    $this->determinePropertyDataClassName($displayOrderDataClass), $propertyName
                )
            );
        }

        return $displayOrderDataClassProperties;
    }

    /**
     * @param \Chamilo\Libraries\Storage\DataClass\Interfaces\DataClassDisplayOrderSupport $displayOrderDataClass
     *
     * @return \Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable
     */
    protected function getDisplayOrderPropertyConditionVariable(DataClassDisplayOrderSupport $displayOrderDataClass)
    {
        return new PropertyConditionVariable(
            $this->determinePropertyDataClassName($displayOrderDataClass),
            $displayOrderDataClass->getDisplayOrderPropertyName()
        );
    }

    /**
     * @param \Chamilo\Libraries\Storage\DataClass\Interfaces\DataClassDisplayOrderSupport $displayOrderDataClass
     * @param integer $operator
     * @param integer $displayOrder
     *
     * @return \Chamilo\Libraries\Storage\Query\Condition\ComparisonCondition
     */
    protected function getDisplayOrderUpdateComparisonCondition(
        DataClassDisplayOrderSupport $displayOrderDataClass, int $operator, int $displayOrder
    )
    {
        return new ComparisonCondition(
            $this->getDisplayOrderPropertyConditionVariable($displayOrderDataClass), $operator,
            new StaticConditionVariable($displayOrder)
        );
    }

    /**
     * @param \Chamilo\Libraries\Storage\DataClass\Interfaces\DataClassDisplayOrderSupport $displayOrderDataClass
     * @param integer $additionValue
     *
     * @return \Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties
     */
    protected function getDisplayOrderUpdateDataClassProperties(
        DataClassDisplayOrderSupport $displayOrderDataClass, int $additionValue
    )
    {
        $displayOrderPropertyConditionVariable =
            $this->getDisplayOrderPropertyConditionVariable($displayOrderDataClass);

        $updateVariable = new OperationConditionVariable(
            $displayOrderPropertyConditionVariable, OperationConditionVariable::ADDITION,
            new StaticConditionVariable($additionValue)
        );

        return new DataClassProperties(
            array(new DataClassProperty($displayOrderPropertyConditionVariable, $updateVariable))
        );
    }

    /**
     * @param \Chamilo\Libraries\Storage\DataClass\Interfaces\DataClassDisplayOrderSupport $displayOrderDataClass
     * @param integer $start
     * @param integer $end
     *
     * @return boolean
     */
    public function updateDisplayOrders(
        DataClassDisplayOrderSupport $displayOrderDataClass, int $start = 1, int $end = null
    )
    {
        $displayOrderDataClassName = $this->determinePropertyDataClassName($displayOrderDataClass);

        if ($start == $end)
        {
            return false;
        }

        $displayOrderPropertyConditionVariable =
            $this->getDisplayOrderPropertyConditionVariable($displayOrderDataClass);

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

        $displayOrderCondition = $this->getDisplayOrderCondition($displayOrderDataClass);

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

        return $this->getDataClassRepository()->updates($displayOrderDataClassName, $properties, $condition);
    }
}