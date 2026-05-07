<?php
namespace Chamilo\Application\Calendar\Extension\Google\Component;

use Chamilo\Application\Calendar\Architecture\Enum\ActionEnum;
use Chamilo\Application\Calendar\Extension\Google\Manager;
use Chamilo\Application\Calendar\Extension\Google\Service\CalendarService;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Interface\ApplicationInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

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
        try {
            $this->calendarService->login(
                $currentUser, $this->getRequest()->query->get(CalendarService::PARAM_AUTHORIZATION_CODE)
            );

            return new RedirectResponse(
                $this->getUrlGenerator()->fromParameters(
                    [
                        ApplicationInterface::PARAM_CONTEXT => \Chamilo\Application\Calendar\Manager::CONTEXT,
                        ApplicationInterface::PARAM_ACTION => ActionEnum::AVAILABILITY->value
                    ]
                )
            );
        }
        catch (Throwable) {
            return new RedirectResponse(
                $this->getUrlGenerator()->fromParameters(
                    [
                        ApplicationInterface::PARAM_CONTEXT => \Chamilo\Application\Calendar\Manager::CONTEXT,
                        ApplicationInterface::PARAM_ACTION => ActionEnum::BROWSE->value
                    ]
                )
            );
        }
    }
}
