<?php
namespace Chamilo\Application\Calendar\Component;

use Chamilo\Application\Calendar\Manager;
use Chamilo\Application\Calendar\Service\CalendarRendererProvider;
use Chamilo\Configuration\Configuration;
use Chamilo\Configuration\Storage\DataClass\Registration;
use Chamilo\Core\User\Component\UserSettingsComponent;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\Calendar\Format\Renderer\FormatHtmlRenderer;
use Chamilo\Libraries\Calendar\Renderer\Form\JumpForm;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;
use Chamilo\Libraries\Format\Structure\ActionBar\SplitDropdownButton;
use Chamilo\Libraries\Format\Structure\ActionBar\SubButton;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\Page;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Translation\Translation;

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
     * Runs this component and displays its output.
     */
    public function run()
    {
        $this->checkAuthorization(Manager::context());
        $this->checkLoggedInAs();

        $header = Page::getInstance()->getHeader();
        $header->addCssFile(Theme::getInstance()->getCssPath(self::package(), true) . 'Print.css', 'print');

        $this->set_parameter(FormatHtmlRenderer::PARAM_TYPE, $this->getCurrentRendererType());
        $this->set_parameter(FormatHtmlRenderer::PARAM_TIME, $this->getCurrentRendererTime());

        $html = array();

        $html[] = $this->render_header();
        $html[] = '<div class="row">';
        $html[] = $this->renderNormalCalendar();
        $html[] = '</div>';
        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }

    protected function checkLoggedInAs()
    {
        $sessionUtilities = $this->getSessionUtilities();
        $asAdmin = $sessionUtilities->get('_as_admin');
        if ($asAdmin && $asAdmin > 0)
        {
            $user = \Chamilo\Core\User\Storage\DataManager::retrieve_by_id(User::class, $asAdmin);
            if (! $user instanceof User || ! $user->is_platform_admin())
            {
                throw new NotAllowedException();
            }
        }
    }

    /**
     *
     * @return \Chamilo\Libraries\Platform\Session\SessionUtilities
     */
    protected function getSessionUtilities()
    {
        return $this->getService('chamilo.libraries.platform.session.session_utilities');
    }

    protected function getCalendarDataProvider()
    {
        if (! isset($this->calendarDataProvider))
        {

            $this->calendarDataProvider = new CalendarRendererProvider(
                $this->getService('chamilo.application.calendar.service.visibility_service'),
                \Chamilo\Application\Calendar\Manager::context(),
                $this->getUser(),
                $this->getDisplayParameters());
        }

        return $this->calendarDataProvider;
    }

    /**
     *
     * @return string[]
     */
    protected function getDisplayParameters()
    {
        return array(
            self::PARAM_CONTEXT => self::package(),
            self::PARAM_ACTION => self::ACTION_BROWSE,
            FormatHtmlRenderer::PARAM_TYPE => $this->getCurrentRendererType(),
            FormatHtmlRenderer::PARAM_TIME => $this->getCurrentRendererTime());
    }

    /**
     *
     * @return string
     */
    protected function renderNormalCalendar()
    {
        $viewTableHtmlRenderer = $this->getViewHtmlTableRendererFactory()->getViewHtmlTableRenderer(
            $this->getCurrentRendererType(),
            $this->getCalendarDataProvider(),
            $this->getCurrentRendererTime());

        return $viewTableHtmlRenderer->render(
            $this->getCurrentRendererTime(),
            $this->getDisplayParameters(),
            $this->getViewActions());
    }

    /**
     *
     * @return \Chamilo\Libraries\Calendar\View\Service\ViewHtmlTableRendererFactory
     */
    protected function getViewHtmlTableRendererFactory()
    {
        return $this->getService('chamilo.libraries.calendar.view.service.view_html_table_renderer_factory');
    }

    /**
     *
     * @return \Chamilo\Libraries\Format\Structure\ActionBar\AbstractButtonToolBarItem[] $viewActions
     */
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

    /**
     *
     * @return \Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup
     */
    protected function getGeneralActions()
    {
        $buttonGroup = new ButtonGroup();

        $printUrl = new Redirect(
            array(
                self::PARAM_CONTEXT => self::package(),
                self::PARAM_ACTION => self::ACTION_PRINT,
                FormatHtmlRenderer::PARAM_TYPE => $this->getCurrentRendererType(),
                FormatHtmlRenderer::PARAM_TIME => $this->getCurrentRendererTime()));

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
