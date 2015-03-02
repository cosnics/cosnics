<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Component;

use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Manager;
use Chamilo\Core\Repository\ContentObject\Assignment\Storage\DataClass\Assignment;
use Chamilo\Core\Repository\ContentObject\CalendarEvent\Storage\DataClass\CalendarEvent;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;

/**
 *
 * @package application.weblcms.tool.assignment.php.component Browser for assignments with calendar functionality.
 * @author Joris Willems <joris.willems@gmail.com>
 * @author Alexander Van Paemel
 */
class BrowserComponent extends Manager implements DelegateComponent
{

    public function get_tool_actions()
    {
        $tool_actions = array();

        if ($this->is_allowed(WeblcmsRights :: EDIT_RIGHT))
        {
            $tool_actions[] = new ToolbarItem(
                Translation :: get('ScoresOverview'),
                Theme :: getInstance()->getCommonImagesPath() . 'action_statistics.png',
                $this->get_url(
                    array(
                        \Chamilo\Application\Weblcms\Manager :: PARAM_TOOL => \Chamilo\Application\Weblcms\Manager :: ACTION_REPORTING,
                        \Chamilo\Application\Weblcms\Manager :: PARAM_TEMPLATE_ID => \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Template\AssignmentScoresTemplate :: class_name(),
                        \Chamilo\Application\Weblcms\Manager :: PARAM_TOOL_ACTION => \Chamilo\Application\Weblcms\Tool\Implementation\Reporting\Manager :: ACTION_VIEW)),
                ToolbarItem :: DISPLAY_ICON_AND_LABEL);
        }

        return $tool_actions;
    }

    public function convert_content_object_publication_to_calendar_event($publication, $from_time, $to_time)
    {
        $object = \Chamilo\Core\Repository\Storage\DataManager :: retrieve_content_object(
            $publication[ContentObjectPublication :: PROPERTY_CONTENT_OBJECT_ID],
            Assignment :: class_name());

        $calendar_event = ContentObject :: factory(CalendarEvent :: class_name());

        $calendar_event->set_title($object->get_title());
        $calendar_event->set_description($object->get_description());
        if ($object instanceof Assignment)
        {
            $calendar_event->set_start_date($object->get_start_time());
            $calendar_event->set_end_date($object->get_end_time());
        }
        else
        {
            $calendar_event->set_start_date($object->get_start_date());
            $calendar_event->set_end_date($object->get_end_date());
        }

        $calendar_event->set_frequency(CalendarEvent :: FREQUENCY_NONE);

        return $calendar_event;
    }
}
