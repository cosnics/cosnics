<?php
namespace Chamilo\Application\Weblcms\Renderer\PublicationList\Type;

use Chamilo\Application\Weblcms\Course\Storage\DataClass\CourseUserRelation;
use Chamilo\Application\Weblcms\Renderer\PublicationList\ContentObjectPublicationListRenderer;
use Chamilo\Application\Weblcms\Storage\DataManager;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\Calendar\Renderer\Form\JumpForm;
use Chamilo\Libraries\Calendar\Renderer\Type\ViewRenderer;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\NotCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Application\Weblcms\Service\CalendarRendererProvider;
use Chamilo\Libraries\Calendar\Renderer\Legend;
use Chamilo\Libraries\Calendar\Renderer\Type\ViewRendererFactory;
use Chamilo\Libraries\Calendar\Renderer\Type\View\MiniMonthRenderer;
use Chamilo\Libraries\Platform\Configuration\LocalSetting;

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

    public function getView()
    {
        return Request :: get(ViewRenderer :: PARAM_TYPE, ViewRenderer :: TYPE_MONTH);
    }

    /**
     *
     * @return int
     */
    public function getCurrentRendererTime()
    {
        if (! isset($this->display_time))
        {
            $this->display_time = Request :: get(ViewRenderer :: PARAM_TIME, time());
        }

        return $this->display_time;
    }

    /**
     *
     * @return string
     */
    public function get_current_renderer_type()
    {
        return Request :: get(ViewRenderer :: PARAM_TYPE, ViewRenderer :: TYPE_MONTH);
    }

    public function as_html()
    {
        $displayParameters = $this->get_tool_browser()->get_parameters();

        $dataProvider = new CalendarRendererProvider($this, $this->get_user(), $this->get_user(), $displayParameters);

        $calendarLegend = new Legend($dataProvider);

        $mini_month_calendar = new MiniMonthRenderer($dataProvider, $calendarLegend, $this->getCurrentRendererTime());

        $this->form = new JumpForm($this->get_url(), $this->getCurrentRendererTime());

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

        $view = $this->getView();

        $rendererFactory = new ViewRendererFactory(
            $view,
            $dataProvider,
            $calendarLegend,
            $this->getCurrentRendererTime());
        $renderer = $rendererFactory->getRenderer();

        if ($this->getCurrentRendererType() == ViewRenderer :: TYPE_DAY ||
             $this->getCurrentRendererType() == ViewRenderer :: TYPE_WEEK)
        {
            $renderer->setStartHour(LocalSetting :: get('working_hours_start'));
            $renderer->setEndHour(LocalSetting :: get('working_hours_end'));
            $renderer->setHideOtherHours(LocalSetting :: get('hide_none_working_hours'));
        }

        $html[] = $renderer->render();
        $html[] = '</div>';

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

        $type_url = $this->get_url(array(ViewRenderer :: PARAM_TYPE => ViewRenderer :: MARKER_TYPE));
        $today_url = $this->get_url(
            array(ViewRenderer :: PARAM_TYPE => $this->getView(), ViewRenderer :: PARAM_TIME => time()));

        $renderer_types = array(
            ViewRenderer :: TYPE_MONTH,
            ViewRenderer :: TYPE_WEEK,
            ViewRenderer :: TYPE_DAY,
            ViewRenderer :: TYPE_YEAR,
            ViewRenderer :: TYPE_LIST);

        $renderer_type_items = ViewRenderer :: getToolbarItems($renderer_types, $type_url, $today_url);

        foreach ($renderer_type_items as $renderer_type_item)
        {
            $toolbar->add_item($renderer_type_item);
        }

        $html = array();
        $html[] = '<div class="content_object" style="padding: 10px;">';
        $html[] = '<div class="description">';

        $html[] = $toolbar->as_html();
        $html[] = '</div>';
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }
}
