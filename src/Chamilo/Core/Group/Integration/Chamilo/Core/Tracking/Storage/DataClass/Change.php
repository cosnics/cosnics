<?php
namespace Chamilo\Core\Group\Integration\Chamilo\Core\Tracking\Storage\DataClass;

use Chamilo\Core\Tracking\Storage\DataClass\ChangesTracker;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticColumnConditionVariable;

class Change extends ChangesTracker
{
    const PROPERTY_TARGET_USER_ID = 'target_user_id';

    public function empty_tracker($event)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable($event::class_name(), 'action'),
            new StaticColumnConditionVariable($event->get_name())
        );

        return $this->remove($condition);
    }

    public function export($start_date, $end_date, $event)
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable($event::class_name(), 'action'),
            new StaticColumnConditionVariable($event->get_name())
        );

        return parent::export($start_date, $end_date, $conditions);
    }

    /**
     * Get the default properties of all aggregate trackers.
     *
     * @return array The property names.
     */
    public static function get_default_property_names($extended_property_names = array())
    {
        return parent::get_default_property_names(array(self::PROPERTY_TARGET_USER_ID));
    }

    /**
     * @return string
     */
    public static function get_table_name()
    {
        return 'tracking_group_change';
    }

    /**
     *
     * @return the $user_id
     */
    public function get_target_user_id()
    {
        return $this->get_default_property(self::PROPERTY_TARGET_USER_ID);
    }

    public function is_summary_tracker()
    {
        return false;
    }

    /**
     *
     * @param $user_id the $user_id to set
     */
    public function set_target_user_id($target_user_id)
    {
        $this->set_default_property(self::PROPERTY_TARGET_USER_ID, $target_user_id);
    }

    public function validate_parameters(array $parameters = array())
    {
        parent::validate_parameters($parameters);

        if ($parameters[self::PROPERTY_TARGET_USER_ID])
        {
            $this->set_target_user_id($parameters[self::PROPERTY_TARGET_USER_ID]);
        }
        else
        {
            $this->set_target_user_id(0);
        }
    }
}
