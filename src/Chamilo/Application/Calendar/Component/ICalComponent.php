<?php
namespace Chamilo\Application\Calendar\Component;

use Chamilo\Application\Calendar\Manager;
use Chamilo\Application\Calendar\Service\CalendarRendererProvider;
use Chamilo\Application\Calendar\Repository\CalendarRendererProviderRepository;
use Chamilo\Libraries\Architecture\Interfaces\NoAuthenticationSupport;
use Chamilo\Libraries\Authentication\AuthenticationValidator;
use Chamilo\Configuration\Configuration;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Format\Display;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Calendar\Renderer\Type\ICalRenderer;
use Chamilo\Libraries\Authentication\QueryAuthentication;

/**
 *
 * @package Chamilo\Application\Calendar\Component
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class ICalComponent extends Manager implements NoAuthenticationSupport
{

    /**
     *
     * @var \Chamilo\Application\Calendar\Service\CalendarRendererProvider
     */
    private $calendarRendererProvider;

    public function run()
    {
        $authenticationValidator = new AuthenticationValidator($this->getRequest(), Configuration :: get_instance());

        if (! $authenticationValidator->isAuthenticated())
        {
            $authentication = QueryAuthentication :: factory('SecurityToken', $this->getRequest());
            $user = $authentication->login();

            if ($user instanceof User)
            {
                $this->getApplicationConfiguration()->setUser($user);
                $this->renderCalendar();
                $authentication->logout($user);
            }
            else
            {
                $response = new \Symfony\Component\HttpFoundation\Response();
                $response->setStatusCode(401);
                $response->send();
            }
        }
        else
        {
            if ($this->getRequest()->query->get(self :: PARAM_DOWNLOAD))
            {
                $this->renderCalendar();
            }
            else
            {
                $icalDownloadUrl = new Redirect(
                    array(
                        Application :: PARAM_CONTEXT => self :: package(),
                        self :: PARAM_ACTION => Manager :: ACTION_ICAL,
                        self :: PARAM_DOWNLOAD => 1));

                $icalExternalUrl = new Redirect(
                    array(
                        Application :: PARAM_CONTEXT => self :: package(),
                        self :: PARAM_ACTION => Manager :: ACTION_ICAL,
                        User :: PROPERTY_SECURITY_TOKEN => $this->getUser()->get_security_token()));

                $html = array();

                $html[] = $this->render_header();

                $html[] = Display :: normal_message(
                    Translation :: get('ICalExternalMessage', array('URL' => $icalExternalUrl->getUrl())));

                $html[] = Display :: normal_message(
                    Translation :: get('ICalDownloadMessage', array('URL' => $icalDownloadUrl->getUrl())));

                $includedCalendars = implode(', ', $this->getCalendarRendererProvider()->getInternalSourceNames());

                $html[] = Display :: warning_message(
                    Translation :: get('ICalWarningMessage', array('INCLUDED_CALENDARS' => $includedCalendars)));

                $html[] = $this->render_footer();

                return implode(PHP_EOL, $html);
            }
        }
    }

    /**
     *
     * @return \Chamilo\Application\Calendar\Service\CalendarRendererProvider
     */
    private function getCalendarRendererProvider()
    {
        if (! isset($this->calendarRendererProvider))
        {
            $this->calendarRendererProvider = new CalendarRendererProvider(
                new CalendarRendererProviderRepository(),
                $this->get_user(),
                $this->get_user(),
                array(),
                \Chamilo\Application\Calendar\Ajax\Manager :: context());
        }

        return $this->calendarRendererProvider;
    }

    private function renderCalendar()
    {
        $icalRenderer = new ICalRenderer($this->getCalendarRendererProvider());
        $icalRenderer->renderAndSend();
    }
}