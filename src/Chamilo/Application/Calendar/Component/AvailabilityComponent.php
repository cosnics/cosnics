<?php
namespace Chamilo\Application\Calendar\Component;

use Chamilo\Application\Calendar\Form\AvailabilityForm;
use Chamilo\Application\Calendar\Manager;
use Chamilo\Application\Calendar\Service\AvailabilityService;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\File\Redirect;

/**
 *
 * @package Chamilo\Application\Calendar\Component
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class AvailabilityComponent extends Manager
{

    /**
     *
     * @return \Chamilo\Application\Calendar\Service\AvailabilityService
     */
    protected function getAvailabilityService()
    {
        return $this->getService(AvailabilityService::class);
    }

    public function run()
    {
        $this->checkAuthorization(Manager::context());

        $availabilityService = $this->getAvailabilityService();
        $form = $this->getForm($availabilityService);

        if ($form->validate())
        {
            $values = $form->exportValues();
            $result = $availabilityService->setAvailabilities(
                $this->getUser(), $values[AvailabilityService::PROPERTY_CALENDAR]
            );

            $nextAction = new Redirect(array(Application::PARAM_CONTEXT => Manager::context()));
            $nextAction->toUrl();
        }
        else
        {
            $html = array();

            $html[] = $this->render_header();
            $html[] = $form->render();
            $html[] = $this->render_footer();

            return implode(PHP_EOL, $html);
        }
    }

    /**
     *
     * @param \Chamilo\Application\Calendar\Service\AvailabilityService $availabilityService
     *
     * @return \Chamilo\Application\Calendar\Form\AvailabilityForm
     */
    public function getForm(AvailabilityService $availabilityService)
    {
        return new AvailabilityForm($this->get_url(), $this->getUser(), $availabilityService);
    }
}
