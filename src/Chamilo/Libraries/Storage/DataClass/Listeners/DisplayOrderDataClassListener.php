<?php
namespace Chamilo\Libraries\Storage\DataClass\Listeners;

use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
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

    /**
     * The DataClass (must implement the necessary interface)
     *
     * @var \Chamilo\Libraries\Storage\DataClass\Listeners\DisplayOrderDataClassListenerSupport
     */
    private $data_class;

    /**
     * Keeps track of the old display order to know whether or not the display orders need to be adapted
     *
     * @var integer
     */
    private $old_display_order;

    /**
     * Keeps track of the old display order condition when a property from the display order context changes so that we
     * can fix the display orders in the old context
     *
     * @var \Chamilo\Libraries\Storage\Query\Condition\Condition
     */
    private $old_display_order_condition;

    /**
     * Keeps track of whether or not the old display order condition needs to be checked if it's still different then
     * the current display order condition
     *
     * @var boolean
     */
    private $check_display_order_condition;

    /**
     * Constructs this dataclass listener and checks if the dataclass implements the necessary functions
     *
     * @param \Chamilo\Libraries\Storage\DataClass\Listeners\DisplayOrderDataClassListenerSupport $dataClass
     * @throws \Exception
     */
    public function __construct(DisplayOrderDataClassListenerSupport $dataClass)
    {
        if (! $dataClass instanceof DisplayOrderDataClassListenerSupport)
        {
            throw new \Exception(
                Translation::get('InterfaceRequired', array('INTERFACE' => 'DisplayOrderDataClassListener')));
        }

        $this->data_class = $dataClass;
    }

    /**
     *
     * @see \Chamilo\Libraries\Storage\DataClass\Listeners\DataClassListener::on_before_create()
     */
    public function on_before_create()
    {
        $data_class = $this->data_class;
        $data_manager = $data_class->package() . '\Storage\DataManager';

        $data_class->set_default_property(
            $data_class->get_display_order_property()->get_property(),
            $data_manager::retrieve_next_value(
                $data_class->get_display_order_property()->get_class(),
                $data_class->get_display_order_property()->get_property(),
                $this->get_display_order_condition()));

        return true;
    }

    /**
     *
     * @see \Chamilo\Libraries\Storage\DataClass\Listeners\DataClassListener::on_before_update()
     */
    public function on_before_update()
    {
        $data_class = $this->data_class;
        $display_order_property = $data_class->get_display_order_property()->get_property();
        $display_order_value = $data_class->get_default_property($display_order_property);
        $data_manager = $data_class->package() . '\Storage\DataManager';

        if (isset($this->old_display_order_condition))
        {
            $original_value = $this->old_display_order ? $this->old_display_order : $display_order_value;

            if (! $data_manager::move_display_orders(
                $data_class->get_display_order_property()->get_class(),
                $display_order_property,
                $original_value,
                null,
                $this->old_display_order_condition))
            {
                return false;
            }

            $next_display_order = $data_manager::retrieve_next_value(
                $data_class->get_display_order_property()->get_class(),
                $data_class->get_display_order_property()->get_property(),
                $this->get_display_order_condition());

            if (! isset($this->old_display_order) || is_null($display_order_value))
            {
                $data_class->set_default_property($display_order_property, $next_display_order);
            }
            else
            {
                $this->old_display_order = $next_display_order;
            }

            unset($this->old_display_order_condition);
        }

        if (isset($this->old_display_order) && ! is_null($display_order_value))
        {
            if (! $data_manager::move_display_orders(
                $data_class->get_display_order_property()->get_class(),
                $display_order_property,
                $this->old_display_order,
                $display_order_value,
                $this->get_display_order_condition()))
            {
                return false;
            }

            unset($this->old_display_order);
        }

        return true;
    }

    /**
     *
     * @see \Chamilo\Libraries\Storage\DataClass\Listeners\DataClassListener::on_after_delete()
     */
    public function on_after_delete($success)
    {
        $data_class = $this->data_class;
        $display_order_property = $data_class->get_display_order_property()->get_property();
        $data_manager = $data_class->package() . '\Storage\DataManager';

        if ($success)
        {
            $success = $data_manager::move_display_orders(
                $data_class->get_display_order_property()->get_class(),
                $display_order_property,
                $data_class->get_default_property($display_order_property),
                null,
                $this->get_display_order_condition());
        }

        return $success;
    }

    /**
     *
     * @see \Chamilo\Libraries\Storage\DataClass\Listeners\DataClassListener::on_before_set_property()
     */
    public function on_before_set_property($name, $value)
    {
        $initial_value = $this->data_class->get_default_property($name);
        if (is_null($initial_value) || ($initial_value == $value && ! isset($this->old_display_order_condition)))
        {
            return true;
        }

        $data_class = $this->data_class;

        if ($name == $data_class->get_display_order_property()->get_property())
        {
            if (! isset($this->old_display_order))
            {
                $this->old_display_order = $initial_value;
            }
            else
            {
                if ($this->old_display_order == $value)
                {
                    unset($this->old_display_order);
                }
            }
        }

        $display_order_context_properties = array();
        foreach ($data_class->get_display_order_context_properties() as $display_order_context_property)
        {
            $display_order_context_properties[] = $display_order_context_property->get_property();
        }

        if (in_array($name, $display_order_context_properties))
        {
            if (! isset($this->old_display_order_condition))
            {
                $this->old_display_order_condition = $this->get_display_order_condition();
            }
            else
            {
                $this->check_display_order_condition = true;
            }
        }

        return true;
    }

    /**
     *
     * @see \Chamilo\Libraries\Storage\DataClass\Listeners\DataClassListener::on_after_set_property()
     */
    public function on_after_set_property($name, $value)
    {
        if ($this->check_display_order_condition)
        {
            if ($this->get_display_order_condition()->hash() == $this->old_display_order_condition->hash())
            {
                unset($this->old_display_order_condition);
            }
        }

        return true;
    }

    /**
     * Returns the display order condition based on the display order context properties
     *
     * @return \Chamilo\Libraries\Storage\Query\Condition\Condition
     */
    protected function get_display_order_condition()
    {
        $data_class = $this->data_class;
        $properties = $this->data_class->get_display_order_context_properties();

        $conditions = array();

        foreach ($properties as $property)
        {
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable($property->get_class(), $property->get_property()),
                new StaticConditionVariable($data_class->get_default_property($property->get_property())));
        }
        return (count($conditions) > 0) ? new AndCondition($conditions) : null;
    }
}
