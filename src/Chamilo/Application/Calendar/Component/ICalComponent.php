<?php
namespace Chamilo\Application\Calendar\Component;

use Chamilo\Application\Calendar\Architecture\Enum\ActionEnum;
use Chamilo\Application\Calendar\Manager;
use Chamilo\Application\Calendar\Service\CalendarDataProvider;
use Chamilo\Application\Calendar\Storage\Repository\VisibilityRepository;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Domain\ChamiloRequest;
use Chamilo\Libraries\Architecture\Interface\ApplicationInterface;
use Chamilo\Libraries\Calendar\Service\View\ICalCalendarRenderer;
use Chamilo\Libraries\Protocol\Authentication\Architecture\Interface\NoAuthenticationSupportInterface;
use Chamilo\Libraries\Protocol\Authentication\Service\AuthenticationValidator;
use Chamilo\Libraries\Protocol\Authentication\Service\SecurityTokenAuthentication;
use Chamilo\Libraries\Service\Routing\UrlGenerator;
use Chamilo\Libraries\UserInterface\Alert\Architecture\Domain\Alert;
use Chamilo\Libraries\UserInterface\Alert\Architecture\Enum\AlertEnum;
use Chamilo\Libraries\UserInterface\Alert\Service\AlertRenderer;
use Chamilo\Libraries\UserInterface\Layout\Service\ApplicationHeaderRenderer;
use Chamilo\Libraries\UserInterface\Layout\Service\DefaultFooterRenderer;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Application\Calendar\Component
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class ICalComponent extends Manager implements NoAuthenticationSupportInterface
{
    public const string PARAM_DOWNLOAD = 'download';

    public function __construct(
        ChamiloRequest $request, ApplicationHeaderRenderer $applicationHeaderRenderer,
        DefaultFooterRenderer $defaultFooterRenderer, Translator $translator, UrlGenerator $urlGenerator,
        VisibilityRepository $visibilityRepository, protected AlertRenderer $alertRenderer,
        protected AuthenticationValidator $authenticationValidator,
        protected CalendarDataProvider $calendarDataProvider, protected ICalCalendarRenderer $iCalCalendarRenderer,
        protected SecurityTokenAuthentication $securityTokenAuthentication
    )
    {
        parent::__construct(
            $request, $applicationHeaderRenderer, $defaultFooterRenderer, $translator, $urlGenerator,
            $visibilityRepository
        );
    }

    /**
     * @throws \Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAuthenticatedException
     * @throws \Exception
     */
    public function run(?User $currentUser = null): Response
    {
        if (!$this->authenticationValidator->isAuthenticated()) {
            $this->authenticationValidator->validateForAuthentication($this->securityTokenAuthentication, false, false);

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
                        ApplicationInterface::PARAM_CONTEXT => Manager::CONTEXT,
                        self::PARAM_ACTION => ActionEnum::ICAL->value,
                        self::PARAM_DOWNLOAD => 1
                    ]
                );

                $icalExternalUrl = $this->getUrlGenerator()->fromParameters(
                    [
                        ApplicationInterface::PARAM_CONTEXT => Manager::CONTEXT,
                        self::PARAM_ACTION => ActionEnum::ICAL->value,
                        User::PROPERTY_SECURITY_TOKEN => $currentUser->getSecurityToken()
                    ]
                );

                $includedCalendars = implode(', ', $this->calendarDataProvider->getSourceNames());

                $translator = $this->getTranslator();
                $html = [];

                $html[] = $this->renderHeader($currentUser);

                $notificationMessages = [];

                $notificationMessages[] = new Alert(
                    $translator->trans('ICalExternalMessage', ['%Url%' => $icalExternalUrl], Manager::CONTEXT)
                );

                $notificationMessages[] = new Alert(
                    $translator->trans('ICalDownloadMessage', ['%Url%' => $icalDownloadUrl], Manager::CONTEXT)
                );

                $notificationMessages[] = new Alert(
                    $translator->trans('ICalWarningMessage', ['%IncludedCalendars%' => $includedCalendars],
                        Manager::CONTEXT), AlertEnum::WARNING
                );

                foreach ($notificationMessages as $notificationMessage) {
                    $html[] = $this->alertRenderer->render($notificationMessage);
                }
                $html[] = $this->renderFooter();

                return new Response(implode(PHP_EOL, $html));
            }
        }
    }

    /**
     * @throws \Exception
     */
    private function renderCalendar(User $user): Response
    {
        $iCalRenderer = $this->iCalCalendarRenderer;
        $events = $this->calendarDataProvider->getEvents(
            $user, $iCalRenderer->getEventsStartTime(), $iCalRenderer->getEventsEndTime()
        );

        return $iCalRenderer->renderAndGetResponse($events);
    }
}