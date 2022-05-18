<?php
namespace Chamilo\Core\Admin\Integration\Chamilo\Core\Tracking\Storage\DataClass;

use Chamilo\Core\Admin\Integration\Chamilo\Core\Tracking\Storage\DataManager;
use Chamilo\Core\Tracking\Storage\DataClass\SimpleTracker;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\ComparisonCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

class Online extends SimpleTracker
{
    const PARAM_USER = 'user';
    const PARAM_TIME = 'time';
    const PROPERTY_USER_ID = 'user_id';
    const PROPERTY_LAST_ACCESS_DATE = 'last_access_date';

    public static function getDefaultPropertyNames(array $extendedPropertyNames = []): array
    {
        return parent::getDefaultPropertyNames(array(self::PROPERTY_USER_ID, self::PROPERTY_LAST_ACCESS_DATE));
    }

    public function run(array $parameters = [])
    {
        $parameters[self::PARAM_TIME] = time();
        
        $condition = new EqualityCondition(
            new PropertyConditionVariable(self::class, self::PROPERTY_USER_ID),
            new StaticConditionVariable($parameters[self::PARAM_USER]));
        
        $existing_online_tracker = DataManager::retrieve(
            self::class,
            new DataClassRetrieveParameters($condition));
        
        if ($existing_online_tracker)
        {
            $this->validate_parameters($parameters, $existing_online_tracker);
            return $existing_online_tracker->update();
        }
        else
        {
            $this->validate_parameters($parameters, $this);
            return $this->create();
        }
    }

    public function validate_parameters(array $parameters = [], Online $object = null)
    {
        $object->set_user_id($parameters[self::PARAM_USER]);
        $object->set_last_access_date($parameters[self::PARAM_TIME]);
    }
    
    // Properties getters and setters
    public function get_user_id()
    {
        return $this->getDefaultProperty(self::PROPERTY_USER_ID);
    }

    public function set_user_id($user_id)
    {
        $this->setDefaultProperty(self::PROPERTY_USER_ID, $user_id);
    }

    public function get_last_access_date()
    {
        return $this->getDefaultProperty(self::PROPERTY_LAST_ACCESS_DATE);
    }

    public function set_last_access_date($last_access_date)
    {
        $this->setDefaultProperty(self::PROPERTY_LAST_ACCESS_DATE, $last_access_date);
    }

    /**
     * @return string
     */
    public static function getTableName(): string
    {
        return 'tracking_admin_online';
    }
}
