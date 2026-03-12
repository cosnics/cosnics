<?php
namespace Chamilo\Application\Calendar\Component;

use Chamilo\Application\Calendar\Manager;
use Chamilo\Application\Calendar\Service\AvailabilityService;
use Chamilo\Application\Calendar\Storage\Repository\VisibilityRepository;
use Chamilo\Application\Calendar\UserInterface\Form\AvailabilityForm;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Domain\ChamiloRequest;
use Chamilo\Libraries\Architecture\Interface\ApplicationInterface;
use Chamilo\Libraries\Service\Routing\UrlGenerator;
use Chamilo\Libraries\Service\Utilities\ActionResultRenderer;
use Chamilo\Libraries\UserInterface\Layout\Service\ApplicationHeaderRenderer;
use Chamilo\Libraries\UserInterface\Layout\Service\DefaultFooterRenderer;
use Exception;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Application\Calendar\Component
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class AvailabilityComponent extends Manager
{
    protected ActionResultRenderer $actionResultRenderer;

    protected AvailabilityService $availabilityService;

    public function __construct(
        ChamiloRequest $request, ApplicationHeaderRenderer $applicationHeaderRenderer,
        DefaultFooterRenderer $defaultFooterRenderer, Translator $translator,
        VisibilityRepository $visibilityRepository, UrlGenerator $urlGenerator,
        ActionResultRenderer $actionResultRenderer, AvailabilityService $availabilityService
    )
    {
        parent::__construct(
            $request, $applicationHeaderRenderer, $defaultFooterRenderer, $translator, $visibilityRepository,
            $urlGenerator
        );

        $this->actionResultRenderer = $actionResultRenderer;
        $this->availabilityService = $availabilityService;
    }

    /**
     * @throws \Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAllowedException
     * @throws \QuickformException
     * @throws \Exception
     */
    public function run(?User $currentUser = null): Response
    {
        $this->checkAuthorization(Manager::CONTEXT);

        $availabilityService = $this->getAvailabilityService();
        $form = $this->getAvailabilityForm($availabilityService, $currentUser);

        if ($form->validate()) {
            $values = $form->exportValues();
            $result = $availabilityService->setAvailabilities(
                $currentUser, $values[AvailabilityService::PROPERTY_CALENDAR]
            );

            if ($result->hasFailed()) {
                throw new Exception($this->getActionResultRenderer()->getMessage($result));
            }

            return new RedirectResponse(
                $this->getUrlGenerator()->fromParameters(
                    [ApplicationInterface::PARAM_CONTEXT => Manager::CONTEXT]
                )
            );
        }
        else {
            $html = [];

            $html[] = $this->renderHeader($currentUser);
            $html[] = $form->render();
            $html[] = $this->renderFooter();

            return new Response(implode(PHP_EOL, $html));
        }
    }

    protected function getActionResultRenderer(): ActionResultRenderer
    {
        return $this->actionResultRenderer;
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \QuickformException
     */
    public function getAvailabilityForm(AvailabilityService $availabilityService, User $user): AvailabilityForm
    {
        return new AvailabilityForm($this->getUrlGenerator()->fromRequest(), $user, $availabilityService);
    }

    /**
     * @return \Chamilo\Application\Calendar\Service\AvailabilityService
     */
    protected function getAvailabilityService(): AvailabilityService
    {
        return $this->availabilityService;
    }
}
