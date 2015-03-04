<?php
namespace Chamilo\Application\Calendar\Component;

use Chamilo\Application\Calendar\Manager;
use Chamilo\Application\Calendar\Storage\DataClass\Visibility;
use Chamilo\Application\Calendar\Storage\DataManager;
use Chamilo\Configuration\Configuration;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\Calendar\Renderer\Form\JumpForm;
use Chamilo\Libraries\Calendar\Renderer\Renderer;
use Chamilo\Libraries\Calendar\Renderer\Type\MiniMonthRenderer;
use Chamilo\Libraries\Calendar\Table\Type\MiniMonthCalendar;
use Chamilo\Libraries\Format\Structure\ActionBarRenderer;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @package application\calendar
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class BrowserComponent extends Manager implements DelegateComponent,
    \Chamilo\Libraries\Calendar\Renderer\Interfaces\CalendarRenderer,
    \Chamilo\Libraries\Calendar\Renderer\Interfaces\VisibilitySupport,
    \Chamilo\Libraries\Calendar\Event\Interfaces\ActionSupport
{

    /**
     *
     * @var JumpForm
     */
    private $form;

    /**
     *
     * @var int
     */
    private $current_time;

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $this->form = new JumpForm($this->get_url(), $this->get_current_renderer_time());
        if ($this->form->validate())
        {
            $this->current_time = $this->form->get_time();
        }

        $html = array();

        $html[] = $this->render_header();
        $html[] = '<a name="top"></a>';
        $html[] = $this->get_action_bar()->as_html();
        $html[] = '<div id="action_bar_browser">';
        $html[] = $this->get_calendar_html();
        $html[] = '</div>';
        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @return string
     */
    public function get_calendar_html()
    {
        $mini_month_renderer = new MiniMonthRenderer(
            $this,
            $this->get_current_renderer_time(),
            null,
            $this->get_mini_month_mark_period());

        $renderer = \Chamilo\Libraries\Calendar\Renderer\Renderer :: factory(
            $this->get_current_renderer_type(),
            $this,
            $this->get_current_renderer_time());

        $html = array();

        $html[] = '<div class="mini_calendar">';
        $html[] = $mini_month_renderer->render();
        $html[] = $this->form->toHtml();
        $html[] = '</div>';
        $html[] = '<div class="normal_calendar">';
        $html[] = $renderer->render();
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @return \libraries\format\ActionBarRenderer
     */
    public function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);

        foreach ($this->get_extension_actions() as $extension_action)
        {
            $action_bar->add_common_action($extension_action);
        }

        // TODO: implement abstraction here to allow extension-specific actions
        if ($this->get_parameter(Manager :: PARAM_VIEW) == 'list')
        {
            $action_bar->set_search_url($this->get_url());
        }

        $type_url = $this->get_url(
            array(
                Application :: PARAM_ACTION => Manager :: ACTION_BROWSE,
                Renderer :: PARAM_TYPE => Renderer :: MARKER_TYPE));
        $today_url = $this->get_url(
            array(
                Application :: PARAM_ACTION => Manager :: ACTION_BROWSE,
                Renderer :: PARAM_TYPE => $this->get_current_renderer_type(),
                Renderer :: PARAM_TIME => time()));

        $renderer_types = array(
            Renderer :: TYPE_LIST,
            Renderer :: TYPE_MONTH,
            Renderer :: TYPE_WEEK,
            Renderer :: TYPE_DAY,
            Renderer :: TYPE_YEAR);
        $renderer_type_items = Renderer :: get_renderer_toolbar_items($renderer_types, $type_url, $today_url);

        foreach ($renderer_type_items as $renderer_type_item)
        {
            $action_bar->add_tool_action($renderer_type_item);
        }

        return $action_bar;
    }

    public function get_extension_actions()
    {
        $extension_registrations = Configuration :: registrations_by_type(
            \Chamilo\Application\Calendar\Manager :: package() . '\Extension');
        $actions = array();

        foreach ($extension_registrations as $extension_registration)
        {
            $action_renderer_class = $extension_registration->get_context() . '\Actions';
            $action_renderer = new $action_renderer_class($this);
            $actions = $actions + $action_renderer->get();
        }

        return $actions;
    }

    /**
     *
     * @see \libraries\architecture\application\Application::add_additional_breadcrumbs()
     */
    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add_help('calendar_browser');
    }

    /**
     *
     * @see \libraries\architecture\application\Application::get_additional_parameters()
     */
    public function get_additional_parameters()
    {
        return array(Renderer :: PARAM_TYPE, Renderer :: PARAM_TIME);
    }

    /**
     *
     * @see \libraries\calendar\renderer\CalendarRenderer::get_calendar_renderer_events()
     */
    public function get_calendar_renderer_events(\Chamilo\Libraries\Calendar\Renderer\Renderer $renderer, $start_time,
        $end_time)
    {
        return DataManager :: get_events($renderer, $start_time, $end_time);
    }

    /**
     *
     * @see \libraries\calendar\renderer\CalendarRenderer::is_calendar_renderer_source_visible()
     */
    public function is_calendar_renderer_source_visible($source)
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Visibility :: class_name(), Visibility :: PROPERTY_USER_ID),
            new StaticConditionVariable($this->get_user()->get_id()));
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Visibility :: class_name(), Visibility :: PROPERTY_SOURCE),
            new StaticConditionVariable($source));
        $condition = new AndCondition($conditions);

        $visibility = DataManager :: retrieve(Visibility :: class_name(), new DataClassRetrieveParameters($condition));

        return ! $visibility instanceof Visibility;
    }

    /**
     *
     * @see \libraries\calendar\renderer\VisibilitySupport::get_calendar_renderer_visibility_data()
     */
    public function get_calendar_renderer_visibility_data()
    {
        return array();
    }

    /**
     *
     * @see \libraries\calendar\event\ActionSupport::get_calendar_event_actions()
     */
    public function get_calendar_event_actions($event)
    {
        $actions = array();

        if ($event->get_context() == __NAMESPACE__)
        {
            $actions[] = new ToolbarItem(
                Translation :: get('Edit', null, Utilities :: COMMON_LIBRARIES),
                Theme :: getInstance()->getCommonImagePath('action_edit'),
                $this->get_publication_editing_url($event->get_id()),
                ToolbarItem :: DISPLAY_ICON);

            $actions[] = new ToolbarItem(
                Translation :: get('Delete', null, Utilities :: COMMON_LIBRARIES),
                Theme :: getInstance()->getCommonImagePath('action_delete'),
                $this->get_publication_deleting_url($event->get_id()),
                ToolbarItem :: DISPLAY_ICON,
                true);
        }

        return $actions;
    }

    /**
     *
     * @return string
     */
    public function get_current_renderer_type()
    {
        return Request :: get(Renderer :: PARAM_TYPE, Renderer :: TYPE_MONTH);
    }

    /**
     *
     * @return int
     */
    public function get_current_renderer_time()
    {
        if (! isset($this->current_time))
        {
            $this->current_time = Request :: get(Renderer :: PARAM_TIME, time());
        }

        return $this->current_time;
    }

    public function get_mini_month_mark_period()
    {
        switch ($this->get_current_renderer_type())
        {
            case Renderer :: TYPE_DAY :
                return MiniMonthCalendar :: PERIOD_DAY;
            case Renderer :: TYPE_MONTH :
                return MiniMonthCalendar :: PERIOD_MONTH;
            case Renderer :: TYPE_WEEK :
                return MiniMonthCalendar :: PERIOD_WEEK;
            default :
                return MiniMonthCalendar :: PERIOD_DAY;
        }
    }
}
