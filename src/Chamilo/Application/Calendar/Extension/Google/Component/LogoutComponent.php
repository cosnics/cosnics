<?php
namespace Chamilo\Application\Calendar\Extension\Google\Component;

use Chamilo\Application\Calendar\Extension\Google\Manager;
use Chamilo\Application\Calendar\Service\AvailabilityService;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Domain\Application;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * @package Chamilo\Application\Calendar\Extension\Google\Component
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class LogoutComponent extends Manager
{
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
                [Application::PARAM_CONTEXT => \Chamilo\Application\Calendar\Manager::CONTEXT]
            )
        );
    }

    protected function getAvailabilityService(): AvailabilityService
    {
        return $this->getService(AvailabilityService::class);
    }
}
