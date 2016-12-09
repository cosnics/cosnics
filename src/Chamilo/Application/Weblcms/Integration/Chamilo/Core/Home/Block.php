<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Home;

use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

class Block extends \Chamilo\Core\Home\Renderer\Type\Basic\BlockRenderer
{

    public function getLastLogin($user_id)
    {
        return $this->getLoginLogout($user_id, 'login');
    }

    public function getLastLogout($user_id)
    {
        return $this->getLoginLogout($user_id, 'logout');
    }

    protected function getLoginLogout($user_id, $type)
    {
        $order_by = new OrderBy(
            new PropertyConditionVariable(
                \Chamilo\Core\User\Integration\Chamilo\Core\Tracking\Storage\DataClass\LoginLogout::class_name(), 
                \Chamilo\Core\User\Integration\Chamilo\Core\Tracking\Storage\DataClass\LoginLogout::PROPERTY_DATE));
        
        $conditions = array();
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                \Chamilo\Core\User\Integration\Chamilo\Core\Tracking\Storage\DataClass\LoginLogout::class_name(), 
                \Chamilo\Core\User\Integration\Chamilo\Core\Tracking\Storage\DataClass\LoginLogout::PROPERTY_USER_ID), 
            new StaticConditionVariable($user_id));
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                \Chamilo\Core\User\Integration\Chamilo\Core\Tracking\Storage\DataClass\LoginLogout::class_name(), 
                \Chamilo\Core\User\Integration\Chamilo\Core\Tracking\Storage\DataClass\LoginLogout::PROPERTY_TYPE), 
            new StaticConditionVariable($type));
        $condition = new AndCondition($conditions);
        
        $trackers = \Chamilo\Core\Tracking\Storage\DataManager::retrieves(
            \Chamilo\Core\User\Integration\Chamilo\Core\Tracking\Storage\DataClass\LoginLogout::class_name(), 
            new DataClassRetrievesParameters($condition, 1, 0, array($order_by)));
        
        $tracker = $trackers->next_result();
        
        if (is_null($tracker))
        {
            return 0;
        }
        
        return $tracker->get_date();
    }

    public function get_user()
    {
        return $this->getUser();
    }

    public function get_user_id()
    {
        return $this->getUserId();
    }
}
