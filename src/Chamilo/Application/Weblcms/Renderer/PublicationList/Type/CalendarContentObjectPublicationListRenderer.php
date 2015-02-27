<?php
namespace Chamilo\Application\Weblcms\Renderer\PublicationList\Type;

use Chamilo\Application\Weblcms\Course\Storage\DataClass\CourseUserRelation;
use Chamilo\Application\Weblcms\Integration\Chamilo\Libraries\Calendar\Event\Event;
use Chamilo\Application\Weblcms\Integration\Chamilo\Libraries\Calendar\Event\EventParser;
use Chamilo\Application\Weblcms\Renderer\PublicationList\ContentObjectPublicationListRenderer;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Storage\DataManager;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\Calendar\Event\Interfaces\ActionSupport;
use Chamilo\Libraries\Calendar\Renderer\Form\JumpForm;
use Chamilo\Libraries\Calendar\Renderer\Interfaces\CalendarRenderer;
use Chamilo\Libraries\Calendar\Renderer\Renderer;
use Chamilo\Libraries\Calendar\Renderer\Type\MiniMonthRenderer;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\NotCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * $Id: week_calendar_content_object_publication_list_renderer.class.php 216 2009-11-13 14:08:06Z kariboe $
 *
 * @package application.lib.weblcms.browser.list_renderer
 */
/**
 * Interval between sections in the week view of the calendar.
 */
/**
 * Renderer to display events in a week calendar
 */
class CalendarContentObjectPublicationListRenderer extends ContentObjectPublicationListRenderer implements
    DelegateComponent, CalendarRenderer, ActionSupport
{

    /**
     *
     * @var JumpForm
     */
    private $form;

    /**
     * The current time displayed in the calendar
     */
    private $display_time;

    /**
     * Sets the current display time.
     *
     * @param $time int The current display time.
     */
    public function set_display_time($time)
    {
        $this->display_time = $time;
    }

    public function get_display_time()
    {
        return $this->display_time;
    }

    public function get_view()
    {
        return Request :: get(Renderer :: PARAM_TYPE);
    }

    public function get_calendar_renderer_events(\Chamilo\Libraries\Calendar\Renderer\Renderer $renderer, $start_time,
        $end_time)
    {
        $publications = $this->get_publications();
        $events = array();
        foreach ($publications as $publication)
        {
            if (method_exists(
                $this->get_tool_browser()->get_parent(),
                'convert_content_object_publication_to_calendar_event'))
            {
                $object = $this->get_tool_browser()->get_parent()->convert_content_object_publication_to_calendar_event(
                    $publication,
                    $start_time,
                    $end_time);
            }
            else
            {
                $object = $this->get_content_object_from_publication($publication);
            }

            $parser = EventParser :: factory($object, $start_time, $end_time, Event :: class_name());
            $parsed_events = $parser->get_events();

            foreach ($parsed_events as &$parsed_event)
            {
                $parameters = array();
                $parameters[Application :: PARAM_CONTEXT] = \Chamilo\Application\Weblcms\Manager :: context();
                $parameters[Application :: PARAM_ACTION] = \Chamilo\Application\Weblcms\Manager :: ACTION_VIEW_COURSE;
                $parameters[\Chamilo\Application\Weblcms\Manager :: PARAM_TOOL_ACTION] = \Chamilo\Application\Weblcms\Tool\Manager :: ACTION_VIEW;
                $parameters[\Chamilo\Application\Weblcms\Tool\Manager :: PARAM_BROWSER_TYPE] = ContentObjectPublicationListRenderer :: TYPE_CALENDAR;
                $parameters[\Chamilo\Application\Weblcms\Manager :: PARAM_COURSE] = $publication[ContentObjectPublication :: PROPERTY_COURSE_ID];
                $parameters[\Chamilo\Application\Weblcms\Manager :: PARAM_TOOL] = $publication[ContentObjectPublication :: PROPERTY_TOOL];
                $parameters[\Chamilo\Application\Weblcms\Manager :: PARAM_PUBLICATION] = $publication[ContentObjectPublication :: PROPERTY_ID];
                $link = Redirect :: get_link($parameters);
                $parsed_event->set_url($link);
                $parsed_event->set_source(
                    Translation :: get('TypeName', null, $this->get_tool_browser()->get_parent()->context()));
                $parsed_event->set_id($publication[ContentObjectPublication :: PROPERTY_ID]);
                $parsed_event->set_context(\Chamilo\Application\Weblcms\Manager :: context());
                $parsed_event->set_course_id($publication[ContentObjectPublication :: PROPERTY_COURSE_ID]);
                $result[] = $parsed_event;
            }
        }

        return $result;
    }

    /**
     *
     * @return int
     */
    public function get_current_renderer_time()
    {
        if (! isset($this->display_time))
        {
            $this->display_time = Request :: get(Renderer :: PARAM_TIME, time());
        }

        return $this->display_time;
    }

    /**
     *
     * @return string
     */
    public function get_current_renderer_type()
    {
        return Request :: get(Renderer :: PARAM_TYPE, Renderer :: TYPE_MONTH);
    }

    public function as_html()
    {
        $mini_month_calendar = new MiniMonthRenderer($this, $this->get_current_renderer_time());

        $this->form = new JumpForm($this->get_url(), $this->get_current_renderer_time());

        if ($this->form->validate())
        {
            $this->display_time = $this->form->get_time();
        }

        $html = array();
        $html[] = '<div class="mini_calendar">';
        $html[] = $mini_month_calendar->render();
        $html[] = $this->form->render();
        $html[] = $this->list_views();

        $html[] = '</div>';
        $html[] = '<div class="normal_calendar">';

        $view = $this->get_view();

        switch ($view)
        {
            case \Chamilo\Libraries\Calendar\Renderer\Renderer :: TYPE_DAY :
                $renderer = \Chamilo\Libraries\Calendar\Renderer\Renderer :: factory(
                    \Chamilo\Libraries\Calendar\Renderer\Renderer :: TYPE_DAY,
                    $this,
                    $this->get_current_renderer_time());

                break;
            case \Chamilo\Libraries\Calendar\Renderer\Renderer :: TYPE_WEEK :
                $renderer = \Chamilo\Libraries\Calendar\Renderer\Renderer :: factory(
                    \Chamilo\Libraries\Calendar\Renderer\Renderer :: TYPE_WEEK,
                    $this,
                    $this->get_current_renderer_time());
                break;
            case \Chamilo\Libraries\Calendar\Renderer\Renderer :: TYPE_MONTH :
                $renderer = \Chamilo\Libraries\Calendar\Renderer\Renderer :: factory(
                    \Chamilo\Libraries\Calendar\Renderer\Renderer :: TYPE_MONTH,
                    $this,
                    $this->get_current_renderer_time());
                break;
            case \Chamilo\Libraries\Calendar\Renderer\Renderer :: TYPE_YEAR :
                $renderer = \Chamilo\Libraries\Calendar\Renderer\Renderer :: factory(
                    \Chamilo\Libraries\Calendar\Renderer\Renderer :: TYPE_YEAR,
                    $this,
                    $this->get_current_renderer_time());
                break;
            default :
                $renderer = \Chamilo\Libraries\Calendar\Renderer\Renderer :: factory(
                    \Chamilo\Libraries\Calendar\Renderer\Renderer :: TYPE_MONTH,
                    $this,
                    $this->get_current_renderer_time());
                break;
        }

        $html[] = $renderer->render();
        $html[] = '</div>';

        $html[] = $this->render_upcoming_events();

        return implode(PHP_EOL, $html);
    }

    public function get_filter_targets()
    {
        $course = $this->get_course_id();

        $targets = array();

        $user_conditions = array();
        $user_conditions[] = new EqualityCondition(
            new PropertyConditionVariable(CourseUserRelation :: class_name(), CourseUserRelation :: PROPERTY_COURSE_ID),
            new StaticConditionVariable($course));
        $user_conditions[] = new NotCondition(
            new EqualityCondition(
                new PropertyConditionVariable(CourseUserRelation :: class_name(), CourseUserRelation :: PROPERTY_USER_ID),
                new StaticConditionVariable($this->get_user_id())));
        $user_condition = new AndCondition($user_conditions);

        $user_relations = \Chamilo\Application\Weblcms\Course\Storage\DataManager :: retrieves(
            CourseUserRelation :: class_name(),
            $user_condition);

        if ($user_relations->size() > 0)
        {
            $targets[] = Translation :: get('Users', null, \Chamilo\Core\User\Manager :: context());
            $targets[] = '----------';

            while ($user_relation = $user_relations->next_result())
            {
                $user = $user_relation->get_user_object();

                $targets['user|' . $user->get_id()] = $user->get_fullname() . ' (' . $user->get_username() . ')';
            }
        }

        $groups = DataManager :: retrieves(
            CourseGroup :: class_name(),
            new EqualityCondition(
                new PropertyConditionVariable(CourseGroup :: class_name(), CourseGroup :: PROPERTY_COURSE_CODE),
                new StaticConditionVariable($course)));

        if ($groups->size() > 0)
        {
            if ($user_relations->size() > 0)
            {
                $targets[] = '';
            }

            $targets[] = Translation :: get('Groups', null, 'groups');
            $targets[] = '----------';

            while ($group = $groups->next_result())
            {
                $targets['group|' . $group->get_id()] = $group->get_name();
            }
        }

        return $targets;
    }

    public function list_views()
    {
        $toolbar = new Toolbar(Toolbar :: TYPE_VERTICAL);
        $toolbar->add_item(
            new ToolbarItem(
                Translation :: get('MonthView', null, Utilities :: COMMON_LIBRARIES),
                Theme :: getInstance()->getImagePath('Chamilo\Application\Weblcms', 'tool_calendar_month'),
                $this->get_url(
                    array(
                        Renderer :: PARAM_TYPE => \Chamilo\Libraries\Calendar\Renderer\Renderer :: TYPE_MONTH,
                        'time' => $this->get_display_time())),
                ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        $toolbar->add_item(
            new ToolbarItem(
                Translation :: get('WeekView', null, Utilities :: COMMON_LIBRARIES),
                Theme :: getInstance()->getImagePath('Chamilo\Application\Weblcms', 'tool_calendar_week'),
                $this->get_url(
                    array(
                        Renderer :: PARAM_TYPE => \Chamilo\Libraries\Calendar\Renderer\Renderer :: TYPE_WEEK,
                        'time' => $this->get_display_time())),
                ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        $toolbar->add_item(
            new ToolbarItem(
                Translation :: get('DayView', null, Utilities :: COMMON_LIBRARIES),
                Theme :: getInstance()->getImagePath('Chamilo\Application\Weblcms', 'tool_calendar_day'),
                $this->get_url(
                    array(
                        Renderer :: PARAM_TYPE => \Chamilo\Libraries\Calendar\Renderer\Renderer :: TYPE_DAY,
                        'time' => $this->get_display_time())),
                ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        $toolbar->add_item(
            new ToolbarItem(
                Translation :: get('YearView', null, Utilities :: COMMON_LIBRARIES),
                Theme :: getInstance()->getImagePath('Chamilo\Application\Weblcms', 'tool_calendar_year'),
                $this->get_url(
                    array(
                        Renderer :: PARAM_TYPE => \Chamilo\Libraries\Calendar\Renderer\Renderer :: TYPE_YEAR,
                        'time' => $this->get_display_time())),
                ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        $toolbar->add_item(
            new ToolbarItem(
                Translation :: get('Today', null, Utilities :: COMMON_LIBRARIES),
                Theme :: getInstance()->getImagePath('Chamilo\Application\Weblcms', 'tool_calendar_today'),
                $this->get_url(array(Renderer :: PARAM_TYPE => $this->get_view(), 'time' => time())),
                ToolbarItem :: DISPLAY_ICON_AND_LABEL));

        $html = array();
        $html[] = '<div class="content_object" style="padding: 10px;">';
        $html[] = '<div class="description">';

        $html[] = $toolbar->as_html();
        $html[] = '</div>';
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    public function render_upcoming_events()
    {
        $html = array();

        // $amount_to_show = 5;
        // $events = $this->get_calendar_events(time(), strtotime('+1 Year', time()), $amount_to_show);
        // ksort($events);

        // if (count($events) > 0)
        // {
        // $html[] = '<div class="content_object" style="padding: 10px;">';
        // $html[] = '<div class="title">' . Translation :: get('UpcomingEvents') . '</div>';
        // $html[] = '<div class="description">';

        // $shown = 0;

        // while ($shown < $amount_to_show && $shown < count($events))
        // {
        // $html[] = $this->render_small_event($events[$shown]);
        // $shown ++;
        // }

        // $html[] = '</div>';
        // $html[] = '</div>';
        // }

        return implode(PHP_EOL, $html);
    }

    public function render_small_event($event)
    {
        $feedback_url = $this->get_url(
            array(
                \Chamilo\Application\Weblcms\Tool\Manager :: PARAM_PUBLICATION_ID => $event->get_optional_property(
                    \Chamilo\Application\Weblcms\Tool\Manager :: PARAM_PUBLICATION_ID),
                \Chamilo\Application\Weblcms\Tool\Manager :: PARAM_ACTION => \Chamilo\Application\Weblcms\Tool\Manager :: ACTION_VIEW),
            array(),
            true);

        return '<a href="' . $feedback_url . '">' . date('d/m/y H:i:s -', $event->get_start_date()) . ' ' .
             $event->get_title() . '</a><br />';
    }

    /*
     * (non-PHPdoc) @see \libraries\calendar\event\ActionSupport::get_calendar_event_actions()
     */
    public function get_calendar_event_actions($event)
    {
        $actions = array();

        if ($event->get_context() == __NAMESPACE__)
        {
            $actions[] = new ToolbarItem(
                Translation :: get('Edit', null, Utilities :: COMMON_LIBRARIES),
                Theme :: getInstance()->getCommonImagesPath() . 'action_edit.png',
                $this->get_publication_editing_url($event->get_id()),
                ToolbarItem :: DISPLAY_ICON);

            $actions[] = new ToolbarItem(
                Translation :: get('Delete', null, Utilities :: COMMON_LIBRARIES),
                Theme :: getInstance()->getCommonImagesPath() . 'action_delete.png',
                $this->get_publication_deleting_url($event->get_id()),
                ToolbarItem :: DISPLAY_ICON,
                true);
        }

        return $actions;
    }
}
