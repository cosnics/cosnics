<?php
namespace Chamilo\Application\Calendar\Extension\Google\Component;

use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Application\Calendar\Extension\Google\Manager;
use Chamilo\Application\Calendar\Extension\Google\Service\VisibilityService;
use Chamilo\Application\Calendar\Extension\Google\Repository\VisibilityRepository;
use Chamilo\Application\Calendar\Extension\Google\Service\GoogleCalendarService;
use Chamilo\Application\Calendar\Extension\Google\Form\VisibilityForm;
use Chamilo\Application\Calendar\Extension\Google\Repository\GoogleCalendarRepository;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\File\Redirect;

/**
 *
 * @package Chamilo\Application\Calendar\Extension\Google\Component
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class VisibilityComponent extends Manager implements DelegateComponent
{

    public function run()
    {
        $visibilityService = new VisibilityService(new VisibilityRepository());
        $form = $this->getForm($visibilityService);

        if ($form->validate())
        {
            $values = $form->exportValues();
            $result = $visibilityService->setVisibilities(
                $this->get_user(),
                $values[VisibilityService :: PROPERTY_VISIBLE]);

            // $this->redirect(
            // $result->getMessage(),
            // $result->hasFailed(),
            // array(Application :: PARAM_CONTEXT => \Chamilo\Application\Calendar\Manager :: context()));

            $nextAction = new Redirect(
                array(Application :: PARAM_CONTEXT => \Chamilo\Application\Calendar\Manager :: context()));
            $nextAction->toUrl();
        }
        else
        {

            $html = array();

            $html[] = $this->render_header();
            $html[] = $form->toHtml();
            $html[] = $this->render_footer();

            return implode(PHP_EOL, $html);
        }
    }

    /**
     *
     * @param \Chamilo\Application\Calendar\Extension\Google\Service\VisibilityService $visibilityService
     * @return \Chamilo\Application\Calendar\Extension\Google\Form\VisibilityForm
     */
    private function getForm(VisibilityService $visibilityService)
    {
        $googleCalendarService = new GoogleCalendarService(GoogleCalendarRepository :: getInstance());

        return new VisibilityForm($this->get_url(), $this->get_user(), $visibilityService, $googleCalendarService);
    }
}
