<?php
namespace Chamilo\Application\Calendar\Extension\Google\Component;

use Chamilo\Application\Calendar\Extension\Google\Manager;
use Chamilo\Application\Calendar\Extension\Google\Service\CalendarService;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Format\Breadcrumb\BreadcrumbLessComponentInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 *
 * @package Chamilo\Application\Calendar\Extension\Google\Component
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class LoginComponent extends Manager implements BreadcrumbLessComponentInterface
{
    public function run()
    {
        $isSuccessful = $this->getCalendarService()->login(
            $this->getUser(), $this->getRequest()->query->get(CalendarService::PARAM_AUTHORIZATION_CODE)
        );

        if ($isSuccessful)
        {
            return new RedirectResponse(
                $this->getUrlGenerator()->fromParameters(
                    [
                        Application::PARAM_CONTEXT => \Chamilo\Application\Calendar\Manager::CONTEXT,
                        Application::PARAM_ACTION => \Chamilo\Application\Calendar\Manager::ACTION_AVAILABILITY
                    ]
                )
            );
        }
    }
}
