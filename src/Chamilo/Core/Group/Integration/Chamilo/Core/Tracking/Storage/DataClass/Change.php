<?php
namespace Chamilo\Core\Group\Integration\Chamilo\Core\Tracking\Storage\DataClass;

use Chamilo\Core\Tracking\Storage\DataClass\ChangesTracker;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticColumnConditionVariable;

class Change extends ChangesTracker
{
    const PROPERTY_TARGET_USER_ID = 'target_user_id';

    /**
     * Get the default properties of all aggregate trackers.
     *
     * @return array The property names.
     */
    public static function getDefaultPropertyNames($extendedPropertyNames = []): array
    {
        return parent::getDefaultPropertyNames(array(self::PROPERTY_TARGET_USER_ID));
    }

    /**
     * @return string
     */
    public static function getTableName(): string
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

    public function validate_parameters(array $parameters = [])
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
