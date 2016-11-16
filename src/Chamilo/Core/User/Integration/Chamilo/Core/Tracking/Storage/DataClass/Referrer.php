<?php
namespace Chamilo\Core\User\Integration\Chamilo\Core\Tracking\Storage\DataClass;

use Chamilo\Core\User\Integration\Chamilo\Core\Tracking\Storage\DataManager;
use Chamilo\Core\User\Integration\Chamilo\Core\Tracking\User;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

class Referrer extends User
{

    public function validate_parameters(array $parameters = array())
    {
        $server = $parameters['server'];
        $referer = $server['HTTP_REFERER'];
        
        $this->set_type(self::TYPE_REFERER);
        $this->set_name($referer);
    }

    public function empty_tracker($event)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(self::class_name(), self::PROPERTY_TYPE), 
            new StaticConditionVariable(self::TYPE_REFERER));
        return $this->remove($condition);
    }

    public function export($start_date, $end_date)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(self::class_name(), self::PROPERTY_TYPE), 
            new StaticConditionVariable(self::TYPE_REFERER));
        return DataManager::retrieves(self::class_name(), new DataClassRetrievesParameters($condition));
    }
}
