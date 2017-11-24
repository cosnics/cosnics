<?php
namespace Chamilo\Application\Calendar\Component;

use Chamilo\Application\Calendar\Manager;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Configuration\Configuration;
use Chamilo\Configuration\Storage\DataClass\Registration;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Core\User\Component\UserSettingsComponent;
use Chamilo\Libraries\Format\Structure\ActionBar\SplitDropdownButton;
use Chamilo\Libraries\Format\Structure\ActionBar\SubButton;

/**
 *
 * @package Chamilo\Application\Calendar\Component
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class FullCalendarComponent extends Manager implements DelegateComponent
{

    /**
     *
     * @see \Chamilo\Libraries\Architecture\Application\Application::run()
     */
    public function run()
    {
        $html = array();

        $html[] = $this->render_header();

        $html[] = '<div class="row">';
        $html[] = $this->getFullCalendarRenderer()->render($this->getCurrentRendererTime(), $this->getViewActions());
        $html[] = '</div>';

        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @return \Chamilo\Libraries\Calendar\Renderer\Type\FullCalendarRenderer
     */
    protected function getFullCalendarRenderer()
    {
        return $this->getService('chamilo.application.calendar.service.full_calendar_renderer');
    }

    protected function getViewActions()
    {
        $actions = array();

        $extensionRegistrations = Configuration::registrations_by_type(
            \Chamilo\Application\Calendar\Manager::package() . '\Extension');

        $primaryExtensionActions = array();
        $additionalExtensionActions = array();

        foreach ($extensionRegistrations as $extensionRegistration)
        {
            if ($extensionRegistration[Registration::PROPERTY_STATUS] == 1)
            {
                $actionRendererClass = $extensionRegistration[Registration::PROPERTY_CONTEXT] . '\Actions';
                $actionRenderer = new $actionRendererClass();

                $primaryExtensionActions = array_merge($primaryExtensionActions, $actionRenderer->getPrimary($this));
                $additionalExtensionActions = array_merge(
                    $additionalExtensionActions,
                    $actionRenderer->getAdditional($this));
            }
        }

        $actions = array_merge($actions, $primaryExtensionActions);
        $actions = array_merge($actions, $additionalExtensionActions);

        $actions[] = $this->getGeneralActions();

        return $actions;
    }

    protected function getGeneralActions()
    {
        $buttonGroup = new ButtonGroup();

        $printUrl = new Redirect(
            array(self::PARAM_CONTEXT => self::package(), self::PARAM_ACTION => self::ACTION_PRINT));

        $buttonGroup->addButton(
            new Button(
                Translation::get(self::ACTION_PRINT . 'Component'),
                new FontAwesomeGlyph('print'),
                $printUrl->getUrl()));

        $iCalUrl = new Redirect(
            array(Application::PARAM_CONTEXT => self::package(), self::PARAM_ACTION => Manager::ACTION_ICAL));

        $buttonGroup->addButton(
            new Button(Translation::get('ICalExternal'), new FontAwesomeGlyph('globe'), $iCalUrl->getUrl()));

        $settingsUrl = new Redirect(
            array(
                Application::PARAM_CONTEXT => \Chamilo\Core\User\Manager::context(),
                Application::PARAM_ACTION => \Chamilo\Core\User\Manager::ACTION_USER_SETTINGS,
                UserSettingsComponent::PARAM_CONTEXT => 'Chamilo\Libraries\Calendar'));

        $splitDropdownButton = new SplitDropdownButton(
            Translation::get('ConfigComponent'),
            new FontAwesomeGlyph('cog'),
            $settingsUrl->getUrl());
        $splitDropdownButton->setDropdownClasses('dropdown-menu-right');

        $availabilityUrl = new Redirect(
            array(Application::PARAM_CONTEXT => self::package(), self::PARAM_ACTION => Manager::ACTION_AVAILABILITY));

        $splitDropdownButton->addSubButton(
            new SubButton(
                Translation::get('AvailabilityComponent'),
                new FontAwesomeGlyph('check-circle-o'),
                $availabilityUrl->getUrl()));

        $buttonGroup->addButton($splitDropdownButton);

        return $buttonGroup;
    }
}

