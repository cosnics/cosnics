<?php
namespace Chamilo\Application\Calendar\Component;

use Chamilo\Application\Calendar\Manager;
use Chamilo\Application\Calendar\Service\CalendarDataProvider;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Domain\Application;
use Chamilo\Libraries\Calendar\Service\View\ICalCalendarRenderer;
use Chamilo\Libraries\Protocol\Authentication\Architecture\Interface\NoAuthenticationSupportInterface;
use Chamilo\Libraries\Protocol\Authentication\Service\AuthenticationValidator;
use Chamilo\Libraries\Protocol\Authentication\Service\SecurityTokenAuthentication;
use Chamilo\Libraries\UserInterface\NotificationMessage\Architecture\Domain\NotificationMessage;
use Symfony\Component\HttpFoundation\Response;

/**
 * @package Chamilo\Application\Calendar\Component
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class ICalComponent extends Manager implements NoAuthenticationSupportInterface
{
    public const PARAM_DOWNLOAD = 'download';

    /**
     * @throws \Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAuthenticatedException
     * @throws \Exception
     */
    public function run(?User $currentUser = null): Response
    {
        $authenticationValidator = $this->getAuthenticationValidator();

        if (!$authenticationValidator->isAuthenticated()) {
            $authenticationValidator->validateForAuthentication($this->getSecurityTokenAuthentication(), false, false);

            if ($currentUser instanceof User) {
                return $this->renderCalendar($currentUser);
            }
            else {
                $response = new Response();
                $response->setStatusCode(Response::HTTP_UNAUTHORIZED);

                return $response;
            }
        }
        else {
            if (!$currentUser instanceof User) {
                $response = new Response();
                $response->setStatusCode(Response::HTTP_UNAUTHORIZED);

                return $response;
            }

            if ($this->getRequest()->query->has(self::PARAM_DOWNLOAD)) {
                return ($this->renderCalendar($currentUser));
            }
            else {
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
                        User::PROPERTY_SECURITY_TOKEN => $currentUser->getSecurityToken()
                    ]
                );

                $includedCalendars = implode(', ', $this->getCalendarRendererProvider()->getSourceNames());

                $translator = $this->getTranslator();
                $html = [];

                $html[] = $this->renderHeader($currentUser);

                $notificationMessages = [];

                $notificationMessages[] = new NotificationMessage(
                    $translator->trans('ICalExternalMessage', ['%Url%' => $icalExternalUrl], Manager::CONTEXT)
                );

                $notificationMessages[] = new NotificationMessage(
                    $translator->trans('ICalDownloadMessage', ['%Url%' => $icalDownloadUrl], Manager::CONTEXT)
                );

                $notificationMessages[] = new NotificationMessage(
                    $translator->trans('ICalWarningMessage', ['%IncludedCalendars%' => $includedCalendars],
                        Manager::CONTEXT), NotificationMessage::TYPE_WARNING
                );

                $html[] = $this->getNotificationMessageRenderer()->render($notificationMessages, false);

                $html[] = $this->renderFooter();

                return new Response(implode(PHP_EOL, $html));
            }
        }
    }

    protected function getAuthenticationValidator(): AuthenticationValidator
    {
        return $this->getService(AuthenticationValidator::class);
    }

    private function getCalendarRendererProvider(): CalendarDataProvider
    {
        return $this->getService(CalendarDataProvider::class);
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
    private function renderCalendar(User $user): Response
    {
        $iCalRenderer = $this->getICalCalendarRenderer();
        $events = $this->getCalendarRendererProvider()->getEvents(
            $user, $iCalRenderer->getEventsStartTime(), $iCalRenderer->getEventsEndTime()
        );

        return $iCalRenderer->renderAndGetResponse($events);
    }
}