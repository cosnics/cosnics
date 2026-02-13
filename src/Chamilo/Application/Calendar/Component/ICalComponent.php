<?php
namespace Chamilo\Application\Calendar\Component;

use Chamilo\Application\Calendar\Implementation\Libraries\CalendarRendererProvider;
use Chamilo\Application\Calendar\Manager;
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

    private CalendarRendererProvider $calendarRendererProvider;

    /**
     * @throws \Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAuthenticatedException
     * @throws \Exception
     */
    public function run(): Response
    {
        $authenticationValidator = $this->getAuthenticationValidator();

        if (!$authenticationValidator->isAuthenticated()) {
            $authenticationValidator->validateForAuthentication($this->getSecurityTokenAuthentication(), false, false);

            $user = $this->getUser();

            if ($user instanceof User) {
                return $this->renderCalendar($user);
            }
            else {
                $response = new Response();
                $response->setStatusCode(Response::HTTP_UNAUTHORIZED);

                return $response;
            }
        }
        else {
            if (!$this->getUser() instanceof User) {
                $response = new Response();
                $response->setStatusCode(401);

                return $response;
            }

            if ($this->getRequest()->query->has(self::PARAM_DOWNLOAD)) {
                return ($this->renderCalendar($this->getUser()));
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
                        User::PROPERTY_SECURITY_TOKEN => $this->getUser()->getSecurityToken()
                    ]
                );

                $includedCalendars =
                    implode(', ', $this->getCalendarRendererProvider($this->getUser())->getSourceNames());

                $translator = $this->getTranslator();
                $html = [];

                $html[] = $this->renderHeader();

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

    private function getCalendarRendererProvider(User $user): CalendarRendererProvider
    {
        if (!isset($this->calendarRendererProvider)) {
            $this->calendarRendererProvider = new CalendarRendererProvider(
                $this->getVisibilityRepository(), $user, [], Manager::CONTEXT
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
    private function renderCalendar(User $user): Response
    {
        return $this->getICalCalendarRenderer()->renderAndGetResponse($this->getCalendarRendererProvider($user));
    }
}