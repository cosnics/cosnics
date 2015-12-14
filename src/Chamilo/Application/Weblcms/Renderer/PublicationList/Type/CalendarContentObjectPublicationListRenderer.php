<?php
namespace Chamilo\Application\Weblcms\Renderer\PublicationList\Type;

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
use Chamilo\Application\Weblcms\Storage\DataClass\CourseEntityRelation;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Joins;
use Chamilo\Libraries\Storage\Query\Join;

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
    public function getCurrentRendererType()
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
            $this->display_time = $this->form->getTime();
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
            $renderer->setStartHour(
                LocalSetting :: getInstance()->get('working_hours_start', 'Chamilo\Libraries\Calendar'));
            $renderer->setEndHour(LocalSetting :: getInstance()->get('working_hours_end', 'Chamilo\Libraries\Calendar'));
            $renderer->setHideOtherHours(
                LocalSetting :: getInstance()->get('hide_non_working_hours', 'Chamilo\Libraries\Calendar'));
        }

        $html[] = $renderer->render();
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    public function get_filter_targets()
    {
        $course = $this->get_course_id();

        $targets = array();

        $userConditions = array();
        $userConditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                CourseEntityRelation :: class_name(),
                CourseEntityRelation :: PROPERTY_COURSE_ID),
            new StaticConditionVariable($course));
        $userConditions[] = new NotCondition(
            new EqualityCondition(
                new PropertyConditionVariable(
                    CourseEntityRelation :: class_name(),
                    CourseEntityRelation :: PROPERTY_ENTITY_ID),
                new StaticConditionVariable($this->get_user_id())));
        $userConditions[] = new NotCondition(
            new EqualityCondition(
                new PropertyConditionVariable(
                    CourseEntityRelation :: class_name(),
                    CourseEntityRelation :: PROPERTY_ENTITY_TYPE),
                new StaticConditionVariable(CourseEntityRelation :: ENTITY_TYPE_USER)));
        $userCondition = new AndCondition($userConditions);

        $user_relations = \Chamilo\Application\Weblcms\Course\Storage\DataManager :: retrieves(
            CourseEntityRelation :: class_name(),
            new DataClassRetrievesParameters(new AndCondition($userCondition)));

        $users = \Chamilo\Application\Weblcms\Course\Storage\DataManager :: retrieves(
            User :: class_name(),
            new DataClassRetrievesParameters(
                $userCondition,
                null,
                null,
                array(),
                new Joins(
                    new Join(
                        CourseEntityRelation :: class_name(),
                        new EqualityCondition(
                            new PropertyConditionVariable(User :: class_name(), User :: PROPERTY_ID),
                            new PropertyConditionVariable(
                                CourseEntityRelation :: class_name(),
                                CourseEntityRelation :: PROPERTY_ENTITY_ID))))));

        if ($users->size() > 0)
        {
            $targets[] = Translation :: get('Users', null, \Chamilo\Core\User\Manager :: context());
            $targets[] = '----------';

            while ($user = $users->next_result())
            {
                $targets['user|' . $user->get_id()] = $user->get_fullname() . ' (' . $user->get_username() . ')';
            }
        }

        $groups = DataManager :: retrieves(
            CourseGroup :: class_name(),
            new DataClassRetrievesParameters(
                new EqualityCondition(
                    new PropertyConditionVariable(CourseGroup :: class_name(), CourseGroup :: PROPERTY_COURSE_CODE),
                    new StaticConditionVariable($course))));

        if ($groups->size() > 0)
        {
            if ($users->size() > 0)
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
