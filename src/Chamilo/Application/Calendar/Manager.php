<?php
namespace Chamilo\Application\Calendar;

use Chamilo\Application\Calendar\Manager;
use Chamilo\Configuration\Configuration;
use Chamilo\Configuration\Storage\DataClass\Registration;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Calendar\Renderer\Type\ViewRenderer;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Tabs\DynamicVisualTab;
use Chamilo\Libraries\Format\Tabs\DynamicVisualTabsRenderer;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Platform\Configuration\LocalSetting;
use Chamilo\Core\User\Component\UserSettingsComponent;

/**
 *
 * @package application\calendar
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
abstract class Manager extends Application
{
    // Parameters
    const PARAM_TIME = 'time';
    const PARAM_VIEW = 'view';
    const PARAM_DOWNLOAD = 'download';

    // Actions
    const ACTION_BROWSE = 'Browser';
    const ACTION_AVAILABILITY = 'Availability';
    const ACTION_ICAL = 'ICal';
    const ACTION_PRINT = 'Printer';

    // Default action
    const DEFAULT_ACTION = self :: ACTION_BROWSE;

    /**
     *
     * @var \Chamilo\Libraries\Format\Tabs\DynamicVisualTabsRenderer
     */
    private $tabs;

    /**
     *
     * @var integer
     */
    private $currentTime;

    /**
     *
     * @return \Chamilo\Libraries\Format\Tabs\DynamicVisualTabsRenderer
     */
    public function getTabs()
    {
        if (! isset($this->tabs))
        {
            $this->tabs = new DynamicVisualTabsRenderer('calendar');

            $this->addTypeTabs($this->tabs);
            $this->addGeneralTabs($this->tabs);
            $this->addExtensionTabs($this->tabs);
        }

        return $this->tabs;
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
    private function addGeneralTabs(DynamicVisualTabsRenderer $tabs)
    {
        $currentAction = $this->getRequest()->query->get(self :: PARAM_ACTION);

        $settingsUrl = new Redirect(
            array(
                Application :: PARAM_CONTEXT => \Chamilo\Core\User\Manager :: context(),
                Application :: PARAM_ACTION => \Chamilo\Core\User\Manager :: ACTION_USER_SETTINGS,
                UserSettingsComponent :: PARAM_CONTEXT => 'Chamilo\Libraries\Calendar'));

        $tabs->add_tab(
            new DynamicVisualTab(
                'configuration',
                Translation :: get('ConfigComponent'),
                Theme :: getInstance()->getImagePath(self :: package(), 'Tab/Configuration'),
                $settingsUrl->getUrl(),
                false,
                false,
                DynamicVisualTab :: POSITION_RIGHT,
                DynamicVisualTab :: DISPLAY_BOTH_SELECTED));

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
                $currentAction == self :: ACTION_AVAILABILITY,
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
                $currentAction == self :: ACTION_ICAL,
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

        $printUrl = new Redirect(
            array(
                self :: PARAM_CONTEXT => self :: package(),
                self :: PARAM_ACTION => self :: ACTION_PRINT,
                ViewRenderer :: PARAM_TYPE => $this->getCurrentRendererType(),
                ViewRenderer :: PARAM_TIME => $this->getCurrentRendererTime()));

        $tabs->add_tab(
            new DynamicVisualTab(
                self :: ACTION_PRINT,
                Translation :: get(self :: ACTION_PRINT . 'Component'),
                Theme :: getInstance()->getImagePath(self :: package(), 'Tab/' . self :: ACTION_PRINT),
                $printUrl->getUrl(),
                $currentAction == self :: ACTION_PRINT,
                false,
                DynamicVisualTab :: POSITION_RIGHT,
                DynamicVisualTab :: DISPLAY_BOTH_SELECTED,
                DynamicVisualTab :: TARGET_POPUP));
    }

    /**
     *
     * @param \Chamilo\Libraries\Format\Tabs\DynamicVisualTabsRenderer $tabs
     */
    private function addExtensionTabs(DynamicVisualTabsRenderer $tabs)
    {
        $extensionRegistrations = Configuration :: registrations_by_type(
            \Chamilo\Application\Calendar\Manager :: package() . '\Extension');

        foreach ($extensionRegistrations as $extensionRegistration)
        {
            $actionRendererClass = $extensionRegistration[Registration :: PROPERTY_CONTEXT] . '\Actions';
            $actionRenderer = new $actionRendererClass($tabs);
            $extensionTabs = $actionRenderer->get($this);

            foreach ($extensionTabs as $extensionTab)
            {
                $tabs->add_tab($extensionTab);
            }
        }
    }

    /**
     *
     * @return string
     */
    public function getCurrentRendererType()
    {
        $requestRendererType = $this->getRequest()->query->get(ViewRenderer :: PARAM_TYPE);

        if (! $requestRendererType)
        {
            return LocalSetting :: getInstance()->get('default_view', 'Chamilo\Libraries\Calendar');
        }

        return $requestRendererType;
    }

    /**
     *
     * @return integer
     */
    public function getCurrentRendererTime()
    {
        if (! isset($this->currentTime))
        {
            $this->currentTime = $this->getRequest()->query->get(ViewRenderer :: PARAM_TIME, time());
        }

        return $this->currentTime;
    }

    /**
     *
     * @param integer $currentTime
     */
    public function setCurrentRendererTime($currentTime)
    {
        $this->currentTime = $currentTime;
    }
}
