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
     * @see \libraries\calendar\event\AjaxVisibility::get_visibility()
     */
    public function retrieve_visibility(Condition $condition)
    {
        return DataManager :: retrieve(Visibility :: class_name(), new DataClassRetrieveParameters($condition));
    }

    /**
     *
     * @see \libraries\calendar\event\AjaxVisibility::create_visibility()
     */
    public function set_visibility($visibility, $data = array())
    {
        return $visibility;
    }
}
