<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\StreamingVideo\Component;

use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Tool\Implementation\StreamingVideo\Manager;
use Chamilo\Core\Repository\ContentObject\CalendarEvent\Storage\DataClass\CalendarEvent;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\DropdownButton;
use Chamilo\Libraries\Format\Structure\ActionBar\SubButton;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Query\Condition\InequalityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\Utilities;

class BrowserComponent extends Manager
{
    const PARAM_FILTER = 'filter';
    const FILTER_TODAY = 'today';
    const FILTER_THIS_WEEK = 'week';
    const FILTER_THIS_MONTH = 'month';

    public function get_tool_actions()
    {
        $toolActions = array();
        $showActions = array();

        $showActions[] = new SubButton(
            Translation :: get('ShowToday', null, Utilities :: COMMON_LIBRARIES),
            Theme :: getInstance()->getCommonImagePath('Action/Browser'),
            $this->get_url(
                array(
                    \Chamilo\Application\Weblcms\Tool\Manager :: PARAM_ACTION => null,
                    self :: PARAM_FILTER => self :: FILTER_TODAY)),
            Button :: DISPLAY_ICON_AND_LABEL);

        $showActions[] = new SubButton(
            Translation :: get('ShowThisWeek', null, Utilities :: COMMON_LIBRARIES),
            Theme :: getInstance()->getCommonImagePath('Action/Browser'),
            $this->get_url(
                array(
                    \Chamilo\Application\Weblcms\Tool\Manager :: PARAM_ACTION => null,
                    self :: PARAM_FILTER => self :: FILTER_THIS_WEEK)),
            Button :: DISPLAY_ICON_AND_LABEL);

        $showActions[] = new SubButton(
            Translation :: get('ShowThisMonth', null, Utilities :: COMMON_LIBRARIES),
            Theme :: getInstance()->getCommonImagePath('Action/Browser'),
            $this->get_url(
                array(
                    \Chamilo\Application\Weblcms\Tool\Manager :: PARAM_ACTION => null,
                    self :: PARAM_FILTER => self :: FILTER_THIS_MONTH)),
            Button :: DISPLAY_ICON_AND_LABEL);

        $showAction = new DropdownButton(
            Translation :: get('Show', null, Utilities :: COMMON_LIBRARIES),
            Theme :: getInstance()->getCommonImagePath('Action/Browser'));
        $showAction->setSubButtons($showActions);

        $toolActions[] = $showAction;

        return $toolActions;
    }

    public function get_tool_conditions()
    {
        $conditions = array();
        $filter = Request :: get(self :: PARAM_FILTER);

        switch ($filter)
        {
            case self :: FILTER_TODAY :
                $time = mktime(0, 0, 0, date('m', time()), date('d', time()), date('Y', time()));

                $conditions[] = new InequalityCondition(
                    new PropertyConditionVariable(
                        ContentObjectPublication :: class_name(),
                        ContentObjectPublication :: PROPERTY_MODIFIED_DATE),
                    InequalityCondition :: GREATER_THAN_OR_EQUAL,
                    new StaticConditionVariable($time));

                break;
            case self :: FILTER_THIS_WEEK :
                $time = strtotime('Next Monday', strtotime('-1 Week', time()));

                $conditions[] = new InequalityCondition(
                    new PropertyConditionVariable(
                        ContentObjectPublication :: class_name(),
                        ContentObjectPublication :: PROPERTY_MODIFIED_DATE),
                    InequalityCondition :: GREATER_THAN_OR_EQUAL,
                    new StaticConditionVariable($time));

                break;
            case self :: FILTER_THIS_MONTH :
                $time = mktime(0, 0, 0, date('m', time()), 1, date('Y', time()));

                $conditions[] = new InequalityCondition(
                    new PropertyConditionVariable(
                        ContentObjectPublication :: class_name(),
                        ContentObjectPublication :: PROPERTY_MODIFIED_DATE),
                    InequalityCondition :: GREATER_THAN_OR_EQUAL,
                    new StaticConditionVariable($time));

                break;
        }

        return $conditions;
    }

    public function convert_content_object_publication_to_calendar_event($publication, $from_time, $to_time)
    {
        $calendar_event = ContentObject :: factory(CalendarEvent :: class_name());

        $calendar_event->set_title($publication[ContentObject :: PROPERTY_TITLE]);
        $calendar_event->set_description($publication[ContentObject :: PROPERTY_DESCRIPTION]);
        $calendar_event->set_start_date($publication[ContentObjectPublication :: PROPERTY_MODIFIED_DATE]);
        $calendar_event->set_end_date($publication[ContentObjectPublication :: PROPERTY_MODIFIED_DATE]);
        $calendar_event->set_frequency(CalendarEvent :: FREQUENCY_NONE);

        return $calendar_event;
    }

    public function get_additional_parameters()
    {
        return array(self :: PARAM_BROWSE_PUBLICATION_TYPE);
    }
}
