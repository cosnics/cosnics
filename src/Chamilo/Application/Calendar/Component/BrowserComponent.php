<?php
namespace Chamilo\Application\Calendar\Component;

use Chamilo\Application\Calendar\Manager;
use Chamilo\Configuration\Configuration;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\Calendar\Renderer\Form\JumpForm;
use Chamilo\Libraries\Calendar\Renderer\Renderer;
use Chamilo\Libraries\Calendar\Renderer\Type\MiniMonthRenderer;
use Chamilo\Libraries\Calendar\Table\Type\MiniMonthCalendar;
use Chamilo\Libraries\Format\Structure\ActionBarRenderer;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Application\Calendar\Extension\Personal\Integration\Chamilo\Application\Calendar\Service\DataProvider;
use Chamilo\Application\Calendar\Extension\Personal\Integration\Chamilo\Application\Calendar\Repository\DataProviderRepository;

/**
 *
 * @package application\calendar
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class BrowserComponent extends Manager implements DelegateComponent
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
    private $currentTime;

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $this->form = new JumpForm($this->get_url(), $this->getCurrentRendererTime());
        if ($this->form->validate())
        {
            $this->currentTime = $this->form->get_time();
        }

        $html = array();

        $html[] = $this->render_header();
        $html[] = '<a name="top"></a>';
        $html[] = $this->getActionBar()->as_html();
        $html[] = '<div id="action_bar_browser">';
        $html[] = $this->getCalendarHtml();
        $html[] = '</div>';
        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @return string
     */
    public function getCalendarHtml()
    {
        $displayParameters = array(
            self :: PARAM_CONTEXT => self :: package(),
            self :: PARAM_ACTION => self :: ACTION_BROWSE,
            Renderer :: PARAM_TYPE => $this->getCurrentRendererType(),
            Renderer :: PARAM_TIME => $this->getCurrentRendererTime());

        $dataProvider = new DataProvider(
            new DataProviderRepository(),
            $this->get_user(),
            $this->get_user(),
            $displayParameters,
            \Chamilo\Application\Calendar\Ajax\Manager :: context());

        $mini_month_renderer = new MiniMonthRenderer(
            $dataProvider,
            $this->getCurrentRendererTime(),
            null,
            $this->getMiniMonthMarkPeriod());

        $renderer = \Chamilo\Libraries\Calendar\Renderer\Renderer :: factory(
            $this->getCurrentRendererType(),
            $dataProvider,
            $this->getCurrentRendererTime());

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
    public function getActionBar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);

        foreach ($this->getExtensionActions() as $extension_action)
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
                Renderer :: PARAM_TYPE => $this->getCurrentRendererType(),
                Renderer :: PARAM_TIME => time()));

        $renderer_types = array(
            Renderer :: TYPE_LIST,
            Renderer :: TYPE_MONTH,
            Renderer :: TYPE_WEEK,
            Renderer :: TYPE_DAY,
            Renderer :: TYPE_YEAR);
        $renderer_type_items = Renderer :: getToolbarItems($renderer_types, $type_url, $today_url);

        foreach ($renderer_type_items as $renderer_type_item)
        {
            $action_bar->add_tool_action($renderer_type_item);
        }

        return $action_bar;
    }

    /**
     *
     * @return string[]
     */
    public function getExtensionActions()
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
     * @see \Chamilo\Libraries\Architecture\Application\Application::add_additional_breadcrumbs()
     */
    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add_help('calendar_browser');
    }

    /**
     *
     * @see \Chamilo\Libraries\Architecture\Application\Application::get_additional_parameters()
     */
    public function get_additional_parameters()
    {
        return array(Renderer :: PARAM_TYPE, Renderer :: PARAM_TIME);
    }

    /**
     *
     * @return string
     */
    public function getCurrentRendererType()
    {
        return Request :: get(Renderer :: PARAM_TYPE, Renderer :: TYPE_MONTH);
    }

    /**
     *
     * @return int
     */
    public function getCurrentRendererTime()
    {
        if (! isset($this->currentTime))
        {
            $this->currentTime = Request :: get(Renderer :: PARAM_TIME, time());
        }

        return $this->currentTime;
    }

    public function getMiniMonthMarkPeriod()
    {
        switch ($this->getCurrentRendererType())
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
