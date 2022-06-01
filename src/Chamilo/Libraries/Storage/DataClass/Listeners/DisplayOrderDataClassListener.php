<?php
namespace Chamilo\Libraries\Storage\DataClass\Listeners;

use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * Dataclass listener which manipulates the crud methods to support common functionality for sort order logic
 *
 * @package Chamilo\Libraries\Storage\DataClass\Listeners
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class DisplayOrderDataClassListener extends DataClassListener
{

    private bool $checkDisplayOrderCondition = false;

    private DisplayOrderDataClassListenerSupport $dataClass;

    private int $oldDisplayOrder;

    private Condition $oldDisplayOrderCondition;

    public function __construct(DisplayOrderDataClassListenerSupport $dataClass)
    {
        $this->dataClass = $dataClass;
    }

    protected function getDisplayOrderCondition(): ?Condition
    {
        $data_class = $this->dataClass;
        $properties = $this->dataClass->getDisplayOrderContextProperties();

        $conditions = [];

        foreach ($properties as $property)
        {
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable($property->getDataClassName(), $property->getPropertyName()),
                new StaticConditionVariable($data_class->getDefaultProperty($property->getPropertyName()))
            );
        }

        return (count($conditions) > 0) ? new AndCondition($conditions) : null;
    }

    /**
     * @throws \ReflectionException
     * @throws \Exception
     */
    public function onAfterDelete(bool $success): bool
    {
        $data_class = $this->dataClass;
        $display_order_property = $data_class->getDisplayOrderProperty()->getPropertyName();

        /**
         * @var \Chamilo\Libraries\Storage\DataManager\DataManager $data_manager
         */
        $data_manager = $data_class->package() . '\Storage\DataManager';

        if ($success)
        {
            $success = $data_manager::move_display_orders(
                $data_class->getDisplayOrderProperty()->getDataClassName(), $display_order_property,
                $data_class->getDefaultProperty($display_order_property), null, $this->getDisplayOrderCondition()
            );
        }

        return $success;
    }

    public function onAfterSetProperty(string $name, $value): bool
    {
        if ($this->checkDisplayOrderCondition)
        {
            if ($this->getDisplayOrderCondition()->hash() == $this->oldDisplayOrderCondition->hash())
            {
                unset($this->oldDisplayOrderCondition);
            }
        }

        return true;
    }

    /**
     * @throws \ReflectionException
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     * @throws \Exception
     */
    public function onBeforeCreate(): bool
    {
        $data_class = $this->dataClass;

        /**
         * @var \Chamilo\Libraries\Storage\DataManager\DataManager $data_manager
         */
        $data_manager = $data_class->package() . '\Storage\DataManager';

        $data_class->setDefaultProperty(
            $data_class->getDisplayOrderProperty()->getPropertyName(), $data_manager::retrieve_next_value(
            $data_class->getDisplayOrderProperty()->getDataClassName(),
            $data_class->getDisplayOrderProperty()->getPropertyName(), $this->getDisplayOrderCondition()
        )
        );

        return true;
    }

    public function onBeforeSetProperty(string $name, $value): bool
    {
        $initial_value = $this->dataClass->getDefaultProperty($name);
        if (is_null($initial_value) || ($initial_value == $value && !isset($this->oldDisplayOrderCondition)))
        {
            return true;
        }

        $data_class = $this->dataClass;

        if ($name == $data_class->getDisplayOrderProperty()->getPropertyName())
        {
            if (!isset($this->oldDisplayOrder))
            {
                $this->oldDisplayOrder = $initial_value;
            }
            else
            {
                if ($this->oldDisplayOrder == $value)
                {
                    unset($this->oldDisplayOrder);
                }
            }
        }

        $display_order_context_properties = [];
        foreach ($data_class->getDisplayOrderContextProperties() as $display_order_context_property)
        {
            $display_order_context_properties[] = $display_order_context_property->getPropertyName();
        }

        if (in_array($name, $display_order_context_properties))
        {
            if (!isset($this->oldDisplayOrderCondition))
            {
                $this->oldDisplayOrderCondition = $this->getDisplayOrderCondition();
            }
            else
            {
                $this->checkDisplayOrderCondition = true;
            }
        }

        return true;
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     * @throws \ReflectionException
     * @throws \Exception
     */
    public function onBeforeUpdate(): bool
    {
        $data_class = $this->dataClass;
        $display_order_property = $data_class->getDisplayOrderProperty()->getPropertyName();
        $display_order_value = $data_class->getDefaultProperty($display_order_property);

        /**
         * @var \Chamilo\Libraries\Storage\DataManager\DataManager $data_manager
         */
        $data_manager = $data_class->package() . '\Storage\DataManager';

        if (isset($this->oldDisplayOrderCondition))
        {
            $original_value = $this->oldDisplayOrder ?: $display_order_value;

            if (!$data_manager::move_display_orders(
                $data_class->getDisplayOrderProperty()->getDataClassName(), $display_order_property, $original_value,
                null, $this->oldDisplayOrderCondition
            ))
            {
                return false;
            }

            $next_display_order = $data_manager::retrieve_next_value(
                $data_class->getDisplayOrderProperty()->getDataClassName(),
                $data_class->getDisplayOrderProperty()->getPropertyName(), $this->getDisplayOrderCondition()
            );

            if (!isset($this->oldDisplayOrder) || is_null($display_order_value))
            {
                $data_class->setDefaultProperty($display_order_property, $next_display_order);
            }
            else
            {
                $this->oldDisplayOrder = $next_display_order;
            }

            unset($this->oldDisplayOrderCondition);
        }

        if (isset($this->oldDisplayOrder) && !is_null($display_order_value))
        {
            if (!$data_manager::move_display_orders(
                $data_class->getDisplayOrderProperty()->getDataClassName(), $display_order_property,
                $this->oldDisplayOrder, $display_order_value, $this->getDisplayOrderCondition()
            ))
            {
                return false;
            }

            unset($this->oldDisplayOrder);
        }

        return true;
    }
}
