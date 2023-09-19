<?php
namespace Chamilo\Application\Calendar\Component;

use Chamilo\Application\Calendar\Manager;
use Chamilo\Application\Calendar\Service\CalendarRendererProvider;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Interfaces\NoAuthenticationSupportInterface;
use Chamilo\Libraries\Authentication\AuthenticationValidator;
use Chamilo\Libraries\Authentication\SecurityToken\SecurityTokenAuthentication;
use Chamilo\Libraries\Calendar\Service\View\ICalCalendarRenderer;
use Chamilo\Libraries\Format\Display;
use Symfony\Component\HttpFoundation\Response;

/**
 * @package Chamilo\Application\Calendar\Component
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class ICalComponent extends Manager implements NoAuthenticationSupportInterface
{

    private CalendarRendererProvider $calendarRendererProvider;

    /**
     * @throws \Chamilo\Libraries\Authentication\AuthenticationException
     * @throws \Exception
     */
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

            if ($this->getRequest()->query->has(self::PARAM_DOWNLOAD))
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

                $translator = $this->getTranslator();
                $html = [];

                $html[] = $this->renderHeader();

                $html[] = Display::normal_message(
                    $translator->trans('ICalExternalMessage', ['URL' => $icalExternalUrl], Manager::CONTEXT)
                );

                $html[] = Display::normal_message(
                    $translator->trans('ICalDownloadMessage', ['URL' => $icalDownloadUrl], Manager::CONTEXT)
                );

                $html[] = Display::warning_message(
                    $translator->trans('ICalWarningMessage', ['INCLUDED_CALENDARS' => $includedCalendars],
                        Manager::CONTEXT)
                );

                $html[] = $this->renderFooter();

                return implode(PHP_EOL, $html);
            }
        }
    }

    protected function getAuthenticationValidator(): AuthenticationValidator
    {
        return $this->getService(AuthenticationValidator::class);
    }

    private function getCalendarRendererProvider(User $user): CalendarRendererProvider
    {
        if (!isset($this->calendarRendererProvider))
        {
            $this->calendarRendererProvider = new CalendarRendererProvider(
                $this->getCalendarRendererProviderRepository(), $user, [],
                \Chamilo\Application\Calendar\Ajax\Manager::CONTEXT
            );
        }

        return $this->calendarRendererProvider;
    }

    public function getICalCalendarRenderer(): ICalCalendarRenderer
    {
        return $this->getService(ICalCalendarRenderer::class);
    }

    protected function getSecurityTokenAuthentication(): SecurityTokenAuthentication
    {
        return $this->getService(SecurityTokenAuthentication::class);
    }

    /**
     * @throws \Exception
     */
    private function renderCalendar(User $user): void
    {
        $this->getICalCalendarRenderer()->renderAndSend($this->getCalendarRendererProvider($user));
    }
}