<?php
namespace Chamilo\Application\Calendar\Component;

use Chamilo\Application\Calendar\Manager;
use Chamilo\Application\Calendar\Repository\CalendarRendererProviderRepository;
use Chamilo\Application\Calendar\Service\CalendarRendererProvider;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Interfaces\NoAuthenticationSupport;
use Chamilo\Libraries\Authentication\AuthenticationValidator;
use Chamilo\Libraries\Authentication\SecurityToken\SecurityTokenAuthentication;
use Chamilo\Libraries\Calendar\Service\View\ICalCalendarRenderer;
use Chamilo\Libraries\Format\Display;
use Chamilo\Libraries\Translation\Translation;
use Symfony\Component\HttpFoundation\Response;

/**
 * @package Chamilo\Application\Calendar\Component
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class ICalComponent extends Manager implements NoAuthenticationSupport
{

    /**
     * @var \Chamilo\Application\Calendar\Service\CalendarRendererProvider
     */
    private $calendarRendererProvider;

    public function run()
    {
        $authenticationValidator = $this->getAuthenticationValidator();

        $alreadyAuthenticated = $authenticationValidator->isAuthenticated();

        $securityCode = $this->getRequest()->getFromRequestOrQuery(User::PROPERTY_SECURITY_TOKEN);
        if (isset($securityCode))
        {
            $authentication = $this->getSecurityTokenAuthentication();
            $authentication->disableAuthSourceCheck();

            $user = $authentication->login();

            if ($user instanceof User)
            {
                $this->renderCalendar($user);
                if (!$alreadyAuthenticated)
                {
                    $authentication->logout($user);
                }
            }
            else
            {
                $response = new Response();
                $response->setStatusCode(401);

                return $response;
            }
        }
        else
        {
            if (!$this->getUser() instanceof User)
            {
                $response = new Response();
                $response->setStatusCode(401);

                return $response;
            }

            if ($this->getRequest()->query->get(self::PARAM_DOWNLOAD))
            {
                $this->renderCalendar($this->getUser());
            }
            else
            {
                $icalDownloadUrl = $this->getUrlGenerator()->fromParameters(
                    [
                        Application::PARAM_CONTEXT => Manager::CONTEXT,
                        self::PARAM_ACTION => Manager::ACTION_ICAL,
                        self::PARAM_DOWNLOAD => 1
                    ]
                );

                $icalExternalUrl = $this->getUrlGenerator()->fromParameters(
                    [
                        Application::PARAM_CONTEXT => Manager::CONTEXT,
                        self::PARAM_ACTION => Manager::ACTION_ICAL,
                        User::PROPERTY_SECURITY_TOKEN => $this->getUser()->get_security_token()
                    ]
                );

                $includedCalendars =
                    implode(', ', $this->getCalendarRendererProvider($this->getUser())->getSourceNames());

                $html = [];

                $html[] = $this->render_header();

                $html[] = Display::normal_message(
                    Translation::get('ICalExternalMessage', ['URL' => $icalExternalUrl])
                );

                $html[] = Display::normal_message(
                    Translation::get('ICalDownloadMessage', ['URL' => $icalDownloadUrl])
                );

                $html[] = Display::warning_message(
                    Translation::get('ICalWarningMessage', ['INCLUDED_CALENDARS' => $includedCalendars])
                );

                $html[] = $this->render_footer();

                return implode(PHP_EOL, $html);
            }
        }
    }

    /**
     * @return AuthenticationValidator
     */
    protected function getAuthenticationValidator()
    {
        return $this->getService(AuthenticationValidator::class);
    }

    /**
     * @param User $user
     *
     * @return CalendarRendererProvider
     */
    private function getCalendarRendererProvider(User $user)
    {
        if (!isset($this->calendarRendererProvider))
        {
            $this->calendarRendererProvider = new CalendarRendererProvider(
                new CalendarRendererProviderRepository(), $user, [], \Chamilo\Application\Calendar\Ajax\Manager::CONTEXT
            );
        }

        return $this->calendarRendererProvider;
    }

    public function getICalCalendarRenderer(): ICalCalendarRenderer
    {
        return $this->getService(ICalCalendarRenderer::class);
    }

    /**
     * @return SecurityTokenAuthentication
     */
    protected function getSecurityTokenAuthentication()
    {
        return $this->getService(SecurityTokenAuthentication::class);
    }

    private function renderCalendar(User $user)
    {
        $this->getICalCalendarRenderer()->renderAndSend($this->getCalendarRendererProvider($user));
    }
}