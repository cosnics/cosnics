<?php
namespace Chamilo\Application\Calendar\Component;

use Chamilo\Application\Calendar\Manager;
use Chamilo\Application\Calendar\Repository\CalendarRendererProviderRepository;
use Chamilo\Application\Calendar\Service\CalendarRendererProvider;
use Chamilo\Configuration\Configuration;
use Chamilo\Configuration\Storage\DataClass\Registration;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\Calendar\Renderer\Form\JumpForm;
use Chamilo\Libraries\Calendar\Renderer\Legend;
use Chamilo\Libraries\Calendar\Renderer\Type\View\MiniMonthRenderer;
use Chamilo\Libraries\Calendar\Renderer\Type\ViewRenderer;
use Chamilo\Libraries\Calendar\Renderer\Type\ViewRendererFactory;
use Chamilo\Libraries\Calendar\Table\Type\MiniMonthCalendar;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Tabs\DynamicVisualTab;
use Chamilo\Libraries\Format\Tabs\DynamicVisualTabsRenderer;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Configuration\LocalSetting;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;

/**
 *
 * @package Chamilo\Application\Calendar\Component
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
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
        $this->set_parameter(ViewRenderer :: PARAM_TYPE, $this->getCurrentRendererType());
        $this->set_parameter(ViewRenderer :: PARAM_TIME, $this->getCurrentRendererTime());

        $this->form = new JumpForm($this->get_url(), $this->getCurrentRendererTime());

        if ($this->form->validate())
        {
            $this->currentTime = $this->form->getTime();
        }

        $html = array();

        $html[] = $this->render_header();
        $html[] = $this->getTabs()->render();
        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }

    public function getTabs()
    {
        $tabs = new DynamicVisualTabsRenderer('calendar');

        $this->addTypeTabs($tabs);
        $this->addGeneralTabs($tabs);
        $this->addExtensionTabs($tabs);

        $tabs->set_content($this->getCalendarHtml());

        return $tabs;
    }

    /**
     *
     * @param \Chamilo\Libraries\Format\Tabs\DynamicVisualTabsRenderer $tabs
     */
    private function addGeneralTabs(DynamicVisualTabsRenderer $tabs)
    {
        $availabilityUrl = new Redirect(
            array(
                Application :: PARAM_CONTEXT => self :: package(),
                self :: PARAM_ACTION => Manager :: ACTION_AVAILABILITY));

        $tabs->add_tab(
            new DynamicVisualTab(
                'availability',
                Translation :: get('AvailabilityComponent'),
                Theme :: getInstance()->getImagePath(self :: package(), 'Tab/Availability'),
                $availabilityUrl->getUrl(),
                false,
                false,
                DynamicVisualTab :: POSITION_RIGHT,
                DynamicVisualTab :: DISPLAY_BOTH_SELECTED));

        $iCalUrl = new Redirect(
            array(Application :: PARAM_CONTEXT => self :: package(), self :: PARAM_ACTION => Manager :: ACTION_ICAL));

        $tabs->add_tab(
            new DynamicVisualTab(
                'ICalExternal',
                Translation :: get('ICalExternal'),
                Theme :: getInstance()->getImagePath(self :: package(), 'Tab/ICalExternal'),
                $iCalUrl->getUrl(),
                false,
                false,
                DynamicVisualTab :: POSITION_RIGHT,
                DynamicVisualTab :: DISPLAY_BOTH_SELECTED));

        $icalDownloadUrl = new Redirect(
            array(
                Application :: PARAM_CONTEXT => self :: package(),
                self :: PARAM_ACTION => Manager :: ACTION_ICAL,
                self :: PARAM_DOWNLOAD => 1));

        $tabs->add_tab(
            new DynamicVisualTab(
                'ICalDownload',
                Translation :: get('ICalDownload'),
                Theme :: getInstance()->getImagePath(self :: package(), 'Tab/ICalDownload'),
                $icalDownloadUrl->getUrl(),
                false,
                false,
                DynamicVisualTab :: POSITION_RIGHT,
                DynamicVisualTab :: DISPLAY_BOTH_SELECTED));
    }

    /**
     *
     * @param \Chamilo\Libraries\Format\Tabs\DynamicVisualTabsRenderer $tabs
     */
    private function addTypeTabs(DynamicVisualTabsRenderer $tabs)
    {
        $typeUrl = $this->get_url(
            array(
                Application :: PARAM_ACTION => Manager :: ACTION_BROWSE,
                ViewRenderer :: PARAM_TYPE => ViewRenderer :: MARKER_TYPE));
        $todayUrl = $this->get_url(
            array(
                Application :: PARAM_ACTION => Manager :: ACTION_BROWSE,
                ViewRenderer :: PARAM_TYPE => $this->getCurrentRendererType(),
                ViewRenderer :: PARAM_TIME => time()));

        $rendererTypes = array(
            ViewRenderer :: TYPE_MONTH,
            ViewRenderer :: TYPE_WEEK,
            ViewRenderer :: TYPE_DAY,
            ViewRenderer :: TYPE_YEAR,
            ViewRenderer :: TYPE_LIST);

        $rendererTypeTabs = ViewRenderer :: getTabs($rendererTypes, $typeUrl, $todayUrl);

        foreach ($rendererTypeTabs as $rendererTypeTab)
        {
            $rendererTypeTab->set_selected($this->getCurrentRendererType() == $rendererTypeTab->get_id());

            $tabs->add_tab($rendererTypeTab);
        }
    }

    /**
     *
     * @param \Chamilo\Libraries\Format\Tabs\DynamicVisualTabsRenderer $tabs
     */
    private function addExtensionTabs(DynamicVisualTabsRenderer $tabs)
    {
        $extensionRegistrations = Configuration :: registrations_by_type(
            \Chamilo\Application\Calendar\Manager :: package() . '\Extension');
        $actions = array();

        foreach ($extensionRegistrations as $extensionRegistration)
        {
            $actionRendererClass = $extensionRegistration[Registration :: PROPERTY_CONTEXT] . '\Actions';
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
            ViewRenderer :: PARAM_TYPE => $this->getCurrentRendererType(),
            ViewRenderer :: PARAM_TIME => $this->getCurrentRendererTime());

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

        $rendererFactory = new ViewRendererFactory(
            $this->getCurrentRendererType(),
            $dataProvider,
            $calendarLegend,
            $this->getCurrentRendererTime());
        $renderer = $rendererFactory->getRenderer();

        if ($this->getCurrentRendererType() == ViewRenderer :: TYPE_DAY ||
             $this->getCurrentRendererType() == ViewRenderer :: TYPE_WEEK)
        {
            $renderer->setStartHour(LocalSetting :: getInstance()->get('working_hours_start'));
            $renderer->setEndHour(LocalSetting :: getInstance()->get('working_hours_end'));
            $renderer->setHideOtherHours(LocalSetting :: getInstance()->get('hide_none_working_hours'));
        }

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
     * @see \Chamilo\Libraries\Architecture\Application\Application::add_additional_breadcrumbs()
     */
    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add_help('calendar_browser');
    }

    /**
     *
     * @return string
     */
    public function getCurrentRendererType()
    {
        return Request :: get(ViewRenderer :: PARAM_TYPE, ViewRenderer :: TYPE_MONTH);
    }

    /**
     *
     * @return int
     */
    public function getCurrentRendererTime()
    {
        if (! isset($this->currentTime))
        {
            $this->currentTime = Request :: get(ViewRenderer :: PARAM_TIME, time());
        }

        return $this->currentTime;
    }

    public function getMiniMonthMarkPeriod()
    {
        switch ($this->getCurrentRendererType())
        {
            case ViewRenderer :: TYPE_DAY :
                return MiniMonthCalendar :: PERIOD_DAY;
            case ViewRenderer :: TYPE_MONTH :
                return MiniMonthCalendar :: PERIOD_MONTH;
            case ViewRenderer :: TYPE_WEEK :
                return MiniMonthCalendar :: PERIOD_WEEK;
            default :
                return MiniMonthCalendar :: PERIOD_DAY;
        }
    }
}
