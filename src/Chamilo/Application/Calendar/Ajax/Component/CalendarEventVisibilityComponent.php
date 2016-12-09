<?php
namespace Chamilo\Application\Calendar\Ajax\Component;

use Chamilo\Application\Calendar\Storage\DataClass\Visibility;
use Chamilo\Application\Calendar\Storage\DataManager;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Query\Condition\Condition;

class CalendarEventVisibilityComponent extends \Chamilo\Libraries\Calendar\Event\Ajax\Component\CalendarEventVisibilityComponent
{

    /**
     *
     * @param Condition $condition
     * @return \Chamilo\Libraries\Storage\DataClass\DataClass
     */
    public function retrieveVisibility(Condition $condition)
    {
        return DataManager::retrieve(Visibility::class_name(), new DataClassRetrieveParameters($condition));
    }
}
