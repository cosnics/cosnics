<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Assessment\Component;

use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Tool\Implementation\Assessment\Manager;
use Chamilo\Core\Repository\ContentObject\CalendarEvent\Storage\DataClass\CalendarEvent;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;

class BrowserComponent extends Manager
{

    public function get_tool_actions()
    {
        $tool_actions = array();

        if ($this->is_allowed(WeblcmsRights :: EDIT_RIGHT))
        {

            $tool_actions[] = new Button(
                Translation :: get('Reporting'),
                Theme :: getInstance()->getCommonImagePath('Action/ViewResults'),
                $this->get_url(
                    array(
                        \Chamilo\Application\Weblcms\Manager :: PARAM_TOOL => 'reporting',
                        \Chamilo\Application\Weblcms\Tool\Manager :: PARAM_ACTION => \Chamilo\Application\Weblcms\Tool\Manager :: ACTION_VIEW,
                        \Chamilo\Core\Reporting\Viewer\Manager :: PARAM_BLOCK_ID => 2)),
                Button :: DISPLAY_ICON_AND_LABEL);
        }

        return $tool_actions;
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

    /**
     * @param BreadcrumbTrail $breadcrumbtrail
     */
    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {

    }
}
