<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Application\PersonalCalendar;

use Chamilo\Application\Calendar\CalendarInterface;
use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\Integration\Chamilo\Libraries\Calendar\Event\Event;
use Chamilo\Application\Weblcms\Integration\Chamilo\Libraries\Calendar\Event\EventParser;
use Chamilo\Application\Weblcms\Renderer\PublicationList\ContentObjectPublicationListRenderer;
use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Core\Repository\ContentObject\CalendarEvent\Storage\DataClass\CalendarEvent;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Condition\SubselectCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

class Manager implements CalendarInterface
{

    public function get_events(\Chamilo\Libraries\Calendar\Renderer\Renderer $renderer, $from_date, $to_date)
    {
        $condition = $this->get_conditions($renderer->get_user());
        $publications = \Chamilo\Application\Weblcms\Storage\DataManager :: retrieves(
            ContentObjectPublication :: class_name(), 
            $condition);
        $result = array();
        while ($publication = $publications->next_result())
        {
            $course = \Chamilo\Application\Weblcms\Storage\DataManager :: retrieve_by_id(
                Course :: class_name(), 
                $publication->get_course_id());
            if (! WeblcmsRights :: get_instance()->is_allowed_in_courses_subtree(
                WeblcmsRights :: VIEW_RIGHT, 
                $publication->get_id(), 
                WeblcmsRights :: TYPE_PUBLICATION, 
                $publication->get_course_id()))
            {
                continue;
            }
            $parser = EventParser :: factory(
                $publication->get_content_object(true), 
                $from_date, 
                $to_date, 
                Event :: class_name());
            $parsed_events = $parser->get_events();
            foreach ($parsed_events as &$parsed_event)
            {
                $parameters = array();
                $parameters[Application :: PARAM_CONTEXT] = \Chamilo\Application\Weblcms\Manager :: context();
                $parameters[Application :: PARAM_ACTION] = \Chamilo\Application\Weblcms\Manager :: ACTION_VIEW_COURSE;
                $parameters[\Chamilo\Application\Weblcms\Manager :: PARAM_TOOL_ACTION] = \Chamilo\Application\Weblcms\Tool\Manager :: ACTION_VIEW;
                $parameters[\Chamilo\Application\Weblcms\Tool\Manager :: PARAM_BROWSER_TYPE] = ContentObjectPublicationListRenderer :: TYPE_CALENDAR;
                $parameters[\Chamilo\Application\Weblcms\Manager :: PARAM_COURSE] = $publication->get_course_id();
                $parameters[\Chamilo\Application\Weblcms\Manager :: PARAM_TOOL] = $publication->get_tool();
                $parameters[\Chamilo\Application\Weblcms\Manager :: PARAM_PUBLICATION] = $publication->get_id();
                $link = Redirect :: get_link($parameters);
                $parsed_event->set_url($link);
                $parsed_event->set_source(
                    Translation :: get('Course', null, \Chamilo\Application\Weblcms\Manager :: context()) . ' - ' .
                         $course->get_title());
                $parsed_event->set_id($publication->get_id());
                $parsed_event->set_context(\Chamilo\Application\Weblcms\Manager :: context());
                $parsed_event->set_course_id($publication->get_course_id());
                $result[] = $parsed_event;
            }
        }
        return $result;
    }

    public function get_conditions($user)
    {
        $user_courses = \Chamilo\Application\Weblcms\Course\Storage\DataManager :: retrieve_all_courses_from_user($user);
        $course_ids = array();
        while ($course = $user_courses->next_result())
        {
            $course_ids[] = $course->get_id();
        }
        $conditions = array();
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectPublication :: class_name(), 
                ContentObjectPublication :: PROPERTY_TOOL), 
            new StaticConditionVariable('calendar'));
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectPublication :: class_name(), 
                ContentObjectPublication :: PROPERTY_HIDDEN), 
            new StaticConditionVariable(0));
        $conditions[] = new InCondition(
            new PropertyConditionVariable(
                ContentObjectPublication :: class_name(), 
                ContentObjectPublication :: PROPERTY_COURSE_ID), 
            $course_ids);
        $subselect_condition = new EqualityCondition(
            new PropertyConditionVariable(ContentObject :: class_name(), ContentObject :: PROPERTY_TYPE), 
            new StaticConditionVariable(CalendarEvent :: class_name()));
        $conditions[] = new SubselectCondition(
            new PropertyConditionVariable(
                ContentObjectPublication :: class_name(), 
                ContentObjectPublication :: PROPERTY_CONTENT_OBJECT_ID), 
            new PropertyConditionVariable(ContentObject :: class_name(), ContentObject :: PROPERTY_ID), 
            ContentObject :: get_table_name(), 
            $subselect_condition, 
            null, 
            \Chamilo\Core\Repository\Storage\DataManager :: get_instance());
        return new AndCondition($conditions);
    }
}
