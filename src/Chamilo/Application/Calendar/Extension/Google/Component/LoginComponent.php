<?php
namespace Chamilo\Application\Calendar\Extension\Google\Component;

use Chamilo\Application\Calendar\Architecture\Enum\ActionEnum;
use Chamilo\Application\Calendar\Extension\Google\Manager;
use Chamilo\Application\Calendar\Extension\Google\Service\CalendarService;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Domain\Application;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 *
 * @package Chamilo\Application\Calendar\Extension\Google\Component
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class LoginComponent extends Manager
{
    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function run(?User $currentUser = null): Response
    {
        $isSuccessful = $this->getCalendarService()->login(
            $currentUser, $this->getRequest()->query->get(CalendarService::PARAM_AUTHORIZATION_CODE)
        );

        if ($isSuccessful) {
            return new RedirectResponse(
                $this->getUrlGenerator()->fromParameters(
                    [
                        Application::PARAM_CONTEXT => \Chamilo\Application\Calendar\Manager::CONTEXT,
                        Application::PARAM_ACTION => ActionEnum::AVAILABILITY->value
                    ]
                )
            );
        }
        else {
            return new RedirectResponse(
                $this->getUrlGenerator()->fromParameters(
                    [
                        Application::PARAM_CONTEXT => \Chamilo\Application\Calendar\Manager::CONTEXT,
                        Application::PARAM_ACTION => ActionEnum::BROWSE->value
                    ]
                )
            );
        }
    }
}
