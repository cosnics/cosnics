<?php
namespace Chamilo\Application\Calendar\Extension\Google\Component;

use Chamilo\Application\Calendar\Extension\Google\Manager;
use Chamilo\Application\Calendar\Extension\Google\Service\CalendarService;
use Chamilo\Application\Calendar\Service\AvailabilityService;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Domain\ChamiloRequest;
use Chamilo\Libraries\Architecture\Interface\ApplicationInterface;
use Chamilo\Libraries\Service\Routing\UrlGenerator;
use Chamilo\Libraries\UserInterface\Layout\Service\ApplicationHeaderRenderer;
use Chamilo\Libraries\UserInterface\Layout\Service\DefaultFooterRenderer;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Application\Calendar\Extension\Google\Component
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class LogoutComponent extends Manager
{
    protected AvailabilityService $availabilityService;

    public function __construct(
        ChamiloRequest $request, ApplicationHeaderRenderer $applicationHeaderRenderer,
        DefaultFooterRenderer $defaultFooterRenderer, Translator $translator, CalendarService $calendarService,
        UrlGenerator $urlGenerator, AvailabilityService $availabilityService
    )
    {
        parent::__construct(
            $request, $applicationHeaderRenderer, $defaultFooterRenderer, $translator, $calendarService, $urlGenerator
        );

        $this->availabilityService = $availabilityService;
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Google_Auth_Exception
     */
    public function run(?User $currentUser = null): Response
    {
        $isSuccessful = $this->getCalendarService()->logout($currentUser);

        if ($isSuccessful) {
            $this->getAvailabilityService()->deleteAvailabilityByCalendarType(Manager::CONTEXT);
        }

        return new RedirectResponse(
            $this->getUrlGenerator()->fromParameters(
                [ApplicationInterface::PARAM_CONTEXT => \Chamilo\Application\Calendar\Manager::CONTEXT]
            )
        );
    }

    protected function getAvailabilityService(): AvailabilityService
    {
        return $this->availabilityService;
    }
}
