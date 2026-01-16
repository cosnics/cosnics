<?php
namespace Chamilo\Application\Calendar\Component;

use Chamilo\Application\Calendar\Manager;
use Chamilo\Application\Calendar\Service\AvailabilityService;
use Chamilo\Application\Calendar\UserInterface\Form\AvailabilityForm;
use Chamilo\Libraries\Architecture\ActionResultRenderer;
use Chamilo\Libraries\Architecture\Application\Application;
use Exception;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * @package Chamilo\Application\Calendar\Component
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class AvailabilityComponent extends Manager
{

    public function run()
    {
        $this->checkAuthorization(Manager::CONTEXT);

        $availabilityService = $this->getAvailabilityService();
        $form = $this->getAvailabilityForm($availabilityService);

        if ($form->validate())
        {
            $values = $form->exportValues();
            $result = $availabilityService->setAvailabilities(
                $this->getUser(), $values[AvailabilityService::PROPERTY_CALENDAR]
            );

            if ($result->hasFailed())
            {
                throw new Exception($this->getActionResultRenderer()->getMessage($result));
            }

            return new RedirectResponse(
                $this->getUrlGenerator()->fromParameters(
                    [Application::PARAM_CONTEXT => Manager::CONTEXT]
                )
            );
        }
        else
        {
            $html = [];

            $html[] = $this->renderHeader();
            $html[] = $form->render();
            $html[] = $this->renderFooter();

            return implode(PHP_EOL, $html);
        }
    }

    protected function getActionResultRenderer(): ActionResultRenderer
    {
        return $this->getService(ActionResultRenderer::class);
    }

    public function getAvailabilityForm(AvailabilityService $availabilityService): AvailabilityForm
    {
        return new AvailabilityForm($this->getUrlGenerator()->fromRequest(), $this->getUser(), $availabilityService);
    }

    /**
     * @return \Chamilo\Application\Calendar\Service\AvailabilityService
     */
    protected function getAvailabilityService(): AvailabilityService
    {
        return $this->getService(AvailabilityService::class);
    }
}
