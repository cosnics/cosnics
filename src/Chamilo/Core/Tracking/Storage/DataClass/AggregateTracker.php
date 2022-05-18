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

    public function run(array $parameters = [])
    {
        $this->validate_parameters($parameters);

        $conditions = [];
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

        if ($tracker_items->count() != 0)
        {
            $current_aggregrate_tracker = $tracker_items->current();
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
    public static function getDefaultPropertyNames(array $extendedPropertyNames = []): array
    {
        return parent::getDefaultPropertyNames(
            array(self::PROPERTY_TYPE, self::PROPERTY_NAME, self::PROPERTY_VALUE)
        );
    }

    public function get_name()
    {
        return $this->getDefaultProperty(self::PROPERTY_NAME);
    }

    public function get_type()
    {
        return $this->getDefaultProperty(self::PROPERTY_TYPE);
    }

    public function get_value()
    {
        return $this->getDefaultProperty(self::PROPERTY_VALUE);
    }

    public function set_name($name)
    {
        $this->setDefaultProperty(self::PROPERTY_NAME, $name);
    }

    public function set_type($type)
    {
        $this->setDefaultProperty(self::PROPERTY_TYPE, $type);
    }

    public function set_value($value)
    {
        $this->setDefaultProperty(self::PROPERTY_VALUE, $value);
    }
}
