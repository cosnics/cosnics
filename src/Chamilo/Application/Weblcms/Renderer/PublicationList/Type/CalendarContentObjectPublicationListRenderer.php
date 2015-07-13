<?php
namespace Chamilo\Application\Weblcms\Renderer\PublicationList\Type;

use Chamilo\Application\Weblcms\Course\Storage\DataClass\CourseUserRelation;
use Chamilo\Application\Weblcms\Renderer\PublicationList\ContentObjectPublicationListRenderer;
use Chamilo\Application\Weblcms\Storage\DataManager;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\Calendar\Renderer\Form\JumpForm;
use Chamilo\Libraries\Calendar\Renderer\Renderer;
use Chamilo\Libraries\Calendar\Renderer\Type\MiniMonthRenderer;
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
use Chamilo\Application\Weblcms\Service\CalendarRendererProvider;
use Chamilo\Libraries\Calendar\Renderer\Legend;

/**
 * Renderer to display events in a week calendar
 */
class CalendarContentObjectPublicationListRenderer extends ContentObjectPublicationListRenderer implements
    DelegateComponent
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
        $displayParameters = $this->get_tool_browser()->get_parameters();

        $dataProvider = new CalendarRendererProvider($this, $this->get_user(), $this->get_user(), $displayParameters);

        $calendarLegend = new Legend($dataProvider);

        $mini_month_calendar = new MiniMonthRenderer($dataProvider, $calendarLegend, $this->get_current_renderer_time());

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
                    $dataProvider,
                    $calendarLegend,
                    $this->get_current_renderer_time());

                break;
            case \Chamilo\Libraries\Calendar\Renderer\Renderer :: TYPE_WEEK :
                $renderer = \Chamilo\Libraries\Calendar\Renderer\Renderer :: factory(
                    \Chamilo\Libraries\Calendar\Renderer\Renderer :: TYPE_WEEK,
                    $dataProvider,
                    $calendarLegend,
                    $this->get_current_renderer_time());
                break;
            case \Chamilo\Libraries\Calendar\Renderer\Renderer :: TYPE_MONTH :
                $renderer = \Chamilo\Libraries\Calendar\Renderer\Renderer :: factory(
                    \Chamilo\Libraries\Calendar\Renderer\Renderer :: TYPE_MONTH,
                    $dataProvider,
                    $calendarLegend,
                    $this->get_current_renderer_time());
                break;
            case \Chamilo\Libraries\Calendar\Renderer\Renderer :: TYPE_YEAR :
                $renderer = \Chamilo\Libraries\Calendar\Renderer\Renderer :: factory(
                    \Chamilo\Libraries\Calendar\Renderer\Renderer :: TYPE_YEAR,
                    $dataProvider,
                    $calendarLegend,
                    $this->get_current_renderer_time());
                break;
            default :
                $renderer = \Chamilo\Libraries\Calendar\Renderer\Renderer :: factory(
                    \Chamilo\Libraries\Calendar\Renderer\Renderer :: TYPE_MONTH,
                    $dataProvider,
                    $calendarLegend,
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
                Theme :: getInstance()->getImagePath('Chamilo\Application\Weblcms', 'Tool/Calendar/Month'),
                $this->get_url(
                    array(
                        Renderer :: PARAM_TYPE => \Chamilo\Libraries\Calendar\Renderer\Renderer :: TYPE_MONTH,
                        'time' => $this->get_display_time())),
                ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        $toolbar->add_item(
            new ToolbarItem(
                Translation :: get('WeekView', null, Utilities :: COMMON_LIBRARIES),
                Theme :: getInstance()->getImagePath('Chamilo\Application\Weblcms', 'Tool/Calendar/Week'),
                $this->get_url(
                    array(
                        Renderer :: PARAM_TYPE => \Chamilo\Libraries\Calendar\Renderer\Renderer :: TYPE_WEEK,
                        'time' => $this->get_display_time())),
                ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        $toolbar->add_item(
            new ToolbarItem(
                Translation :: get('DayView', null, Utilities :: COMMON_LIBRARIES),
                Theme :: getInstance()->getImagePath('Chamilo\Application\Weblcms', 'Tool/Calendar/Day'),
                $this->get_url(
                    array(
                        Renderer :: PARAM_TYPE => \Chamilo\Libraries\Calendar\Renderer\Renderer :: TYPE_DAY,
                        'time' => $this->get_display_time())),
                ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        $toolbar->add_item(
            new ToolbarItem(
                Translation :: get('YearView', null, Utilities :: COMMON_LIBRARIES),
                Theme :: getInstance()->getImagePath('Chamilo\Application\Weblcms', 'Tool/Calendar/Year'),
                $this->get_url(
                    array(
                        Renderer :: PARAM_TYPE => \Chamilo\Libraries\Calendar\Renderer\Renderer :: TYPE_YEAR,
                        'time' => $this->get_display_time())),
                ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        $toolbar->add_item(
            new ToolbarItem(
                Translation :: get('Today', null, Utilities :: COMMON_LIBRARIES),
                Theme :: getInstance()->getImagePath('Chamilo\Application\Weblcms', 'Tool/Calendar/Today'),
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
}
