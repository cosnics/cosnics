<?php
namespace Chamilo\Application\Calendar\Component;

use Chamilo\Application\Calendar\Manager;
use Chamilo\Application\Calendar\Service\AvailabilityService;
use Chamilo\Application\Calendar\Storage\Repository\VisibilityRepository;
use Chamilo\Application\Calendar\UserInterface\Form\AvailabilityFormType;
use Chamilo\Core\User\Storage\Entity\User;
use Chamilo\Libraries\Architecture\Domain\ChamiloRequest;
use Chamilo\Libraries\Architecture\Interface\ApplicationInterface;
use Chamilo\Libraries\Service\Routing\UrlGenerator;
use Chamilo\Libraries\Service\Utilities\ActionResultRenderer;
use Chamilo\Libraries\UserInterface\Layout\Service\ApplicationHeaderRenderer;
use Chamilo\Libraries\UserInterface\Layout\Service\DefaultFooterRenderer;
use Exception;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\Translator;
use Twig\Environment;

/**
 * @package Chamilo\Application\Calendar\Component
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class AvailabilityComponent extends Manager
{
    public function __construct(
        ChamiloRequest $request, ApplicationHeaderRenderer $applicationHeaderRenderer,
        DefaultFooterRenderer $defaultFooterRenderer, Translator $translator, UrlGenerator $urlGenerator,
        VisibilityRepository $visibilityRepository, protected readonly ActionResultRenderer $actionResultRenderer,
        protected readonly AvailabilityService $availabilityService,
        protected readonly FormFactoryInterface $formFactory, protected readonly Environment $twigFormEnvironment
    )
    {
        parent::__construct(
            $request, $applicationHeaderRenderer, $defaultFooterRenderer, $translator, $urlGenerator,
            $visibilityRepository
        );
    }

    /**
     * @throws \Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAllowedException
     * @throws \Exception
     */
    public function run(?User $currentUser = null): Response
    {
        $this->checkAuthorization(Manager::CONTEXT, $currentUser);

        $form = $this->formFactory->create(
            AvailabilityFormType::class, $this->getDefaultAvailabilityData($currentUser),
            ['action' => $this->getUrlGenerator()->fromRequest(), 'user' => $currentUser]
        );
        $form->handleRequest($this->getRequest());

        if ($form->isSubmitted() && $form->isValid()) {
            $result = $this->availabilityService->setAvailabilitiesFromParameters(
                $currentUser, $form->getData()
            );

            if ($result->hasFailed()) {
                throw new Exception($this->actionResultRenderer->getMessage($result));
            }

            return new RedirectResponse(
                $this->getUrlGenerator()->fromParameters(
                    [ApplicationInterface::PARAM_CONTEXT => Manager::CONTEXT]
                )
            );
        }

        $html = [];

        $html[] = $this->renderHeader($currentUser);
        $html[] = $this->twigFormEnvironment->render('form.html.twig', [
            'form' => $form->createView(),
        ]);
        $html[] = $this->renderFooter();

        return new Response(implode(PHP_EOL, $html));
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    protected function getDefaultAvailabilityData(User $user): array
    {
        $defaultData = [];
        $availabilities = $this->availabilityService->getAvailabilitiesForUser($user);

        $calendars = $this->availabilityService->getAvailableCalendars($user);

        foreach ($calendars as $calendarTypeCalendars) {
            foreach ($calendarTypeCalendars as $calendarTypeCalendar) {
                $defaultData[$calendarTypeCalendar->getUniqueIdentifier()] = true;
            }
        }

        foreach ($availabilities as $availability) {
            $defaultData[$availability->getUniqueIdentifier()] = $availability->getAvailability();
        }

        return $defaultData;
    }
}
