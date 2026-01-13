<?php
namespace Chamilo\Application\Calendar\Extension\Google\Component;

use Chamilo\Application\Calendar\Extension\Google\Manager;
use Chamilo\Application\Calendar\Service\AvailabilityService;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Format\Breadcrumb\BreadcrumbLessComponentInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * @package Chamilo\Application\Calendar\Extension\Google\Component
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class LogoutComponent extends Manager implements BreadcrumbLessComponentInterface
{

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exceptions\StorageMethodException
     */
    public function run()
    {
        $isSuccessful = $this->getCalendarService()->logout($this->getUser());

        if ($isSuccessful)
        {
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
