<?php
namespace Chamilo\Application\Calendar\Ajax\Component;

use Chamilo\Application\Calendar\Storage\DataClass\Visibility;
use Chamilo\Application\Calendar\Storage\DataManager;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Query\Condition\Condition;

class CalendarEventVisibilityComponent
    extends \Chamilo\Libraries\Calendar\Ajax\Component\CalendarEventVisibilityComponent
{

    public function retrieveVisibility(Condition $condition): ?\Chamilo\Libraries\Calendar\Event\Visibility
    {
        $visibility = DataManager::retrieve(Visibility::class, new DataClassRetrieveParameters($condition));

        if ($visibility instanceof \Chamilo\Libraries\Calendar\Event\Visibility)
        {
            return $visibility;
        }

        return null;
    }
}
