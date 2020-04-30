<?php
namespace Chamilo\Core\Tracking\Storage\DataClass;

use Chamilo\Libraries\Storage\DataManager\DataManager;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 *
 * @author Hans De Bisschop
 */
abstract class AggregateTracker extends Tracker
{
    const PROPERTY_NAME = 'name';

    const PROPERTY_TYPE = 'type';

    const PROPERTY_VALUE = 'value';

    public function run(array $parameters = array())
    {
        $this->validate_parameters($parameters);

        $conditions = array();
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(static::class, self::PROPERTY_TYPE),
            new StaticConditionVariable($this->get_type())
        );
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(static::class, self::PROPERTY_NAME),
            new StaticConditionVariable($this->get_name())
        );
        $condition = new AndCondition($conditions);

        $tracker_items = DataManager::retrieves(
            static::class, new DataClassRetrievesParameters($condition)
        );

        if ($tracker_items->size() != 0)
        {
            $current_aggregrate_tracker = $tracker_items->next_result();
            $this->set_id($current_aggregrate_tracker->get_id());
            $this->set_value($current_aggregrate_tracker->get_value() + 1);

            return $this->update();
        }
        else
        {
            $this->set_value(1);

            return $this->create();
        }
    }

    /**
     * Get the default properties of all aggregate trackers.
     *
     * @return array The property names.
     */
    public static function get_default_property_names($extended_property_names = array())
    {
        return parent::get_default_property_names(
            array(self::PROPERTY_TYPE, self::PROPERTY_NAME, self::PROPERTY_VALUE)
        );
    }

    public function get_name()
    {
        return $this->get_default_property(self::PROPERTY_NAME);
    }

    public function get_type()
    {
        return $this->get_default_property(self::PROPERTY_TYPE);
    }

    public function get_value()
    {
        return $this->get_default_property(self::PROPERTY_VALUE);
    }

    public function set_name($name)
    {
        $this->set_default_property(self::PROPERTY_NAME, $name);
    }

    public function set_type($type)
    {
        $this->set_default_property(self::PROPERTY_TYPE, $type);
    }

    public function set_value($value)
    {
        $this->set_default_property(self::PROPERTY_VALUE, $value);
    }
}
