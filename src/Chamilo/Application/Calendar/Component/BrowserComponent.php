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
use Chamilo\Application\Calendar\Service\CalendarRendererProvider;
use Chamilo\Application\Calendar\Repository\CalendarRendererProviderRepository;
use Chamilo\Libraries\Calendar\Renderer\Legend;
use Chamilo\Libraries\Calendar\Renderer\RendererFactory;
use Chamilo\Libraries\Format\Tabs\DynamicVisualTabsRenderer;

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

        $html[] = $this->getTabs()->render();

        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }

    public function getTabs()
    {
        $tabs = new DynamicVisualTabsRenderer('calendar');

        $this->addTypeTabs($tabs);
        $this->addExtensionTabs($tabs);

        $tabs->set_content($this->getCalendarHtml());

        return $tabs;
    }

    public function addTypeTabs(DynamicVisualTabsRenderer $tabs)
    {
        $typeUrl = $this->get_url(
            array(
                Application :: PARAM_ACTION => Manager :: ACTION_BROWSE,
                Renderer :: PARAM_TYPE => Renderer :: MARKER_TYPE));
        $todayUrl = $this->get_url(
            array(
                Application :: PARAM_ACTION => Manager :: ACTION_BROWSE,
                Renderer :: PARAM_TYPE => $this->getCurrentRendererType(),
                Renderer :: PARAM_TIME => time()));

        $rendererTypes = array(
            Renderer :: TYPE_MONTH,
            Renderer :: TYPE_WEEK,
            Renderer :: TYPE_DAY,
            Renderer :: TYPE_YEAR,
            Renderer :: TYPE_LIST);

        $rendererTypeTabs = Renderer :: getTabs($rendererTypes, $typeUrl, $todayUrl);

        foreach ($rendererTypeTabs as $rendererTypeTab)
        {
            $rendererTypeTab->set_selected($this->getCurrentRendererType() == $rendererTypeTab->get_id());

            $tabs->add_tab($rendererTypeTab);
        }
    }

    private function addExtensionTabs(DynamicVisualTabsRenderer $tabs)
    {
        $extensionRegistrations = Configuration :: registrations_by_type(
            \Chamilo\Application\Calendar\Manager :: package() . '\Extension');
        $actions = array();

        foreach ($extensionRegistrations as $extensionRegistration)
        {
            $actionRendererClass = $extensionRegistration->get_context() . '\Actions';
            $actionRenderer = new $actionRendererClass($tabs);
            $extensionTabs = $actionRenderer->get();

            foreach ($extensionTabs as $extensionTab)
            {
                $tabs->add_tab($extensionTab);
            }
        }

        return $actions;
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

        $dataProvider = new CalendarRendererProvider(
            new CalendarRendererProviderRepository(),
            $this->get_user(),
            $this->get_user(),
            $displayParameters,
            \Chamilo\Application\Calendar\Ajax\Manager :: context());

        $calendarLegend = new Legend($dataProvider);

        $mini_month_renderer = new MiniMonthRenderer(
            $dataProvider,
            $calendarLegend,
            $this->getCurrentRendererTime(),
            null,
            $this->getMiniMonthMarkPeriod());

        $rendererFactory = new RendererFactory(
            $this->getCurrentRendererType(),
            $dataProvider,
            $calendarLegend,
            $this->getCurrentRendererTime());

        $html = array();

        $html[] = '<div class="mini_calendar">';
        $html[] = $mini_month_renderer->render();
        $html[] = $this->form->toHtml();
        $html[] = '</div>';
        $html[] = '<div class="normal_calendar">';
        $html[] = $rendererFactory->render();
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

        // foreach ($this->getExtensionActions() as $extension_action)
        // {
        // $action_bar->add_common_action($extension_action);
        // }

        // TODO: implement abstraction here to allow extension-specific actions
        // if ($this->get_parameter(Renderer :: PARAM_TYPE) == 'List')
        // {
        // $action_bar->set_search_url($this->get_url());
        // }

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
            $actions = array_merge($actions, $action_renderer->get());
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
