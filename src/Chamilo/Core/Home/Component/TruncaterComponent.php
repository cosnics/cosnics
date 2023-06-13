<?php
namespace Chamilo\Core\Home\Component;

use Chamilo\Core\Home\Manager;
use Chamilo\Core\Home\Service\HomeService;
use Chamilo\Libraries\Architecture\Application\Application;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * @package Chamilo\Core\Home\Component
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class TruncaterComponent extends Manager
{

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function run()
    {
        $this->getHomeService()->deleteElementsForUserIdentifier($this->getUser()->getId());

        return new RedirectResponse(
            $this->getUrlGenerator()->fromParameters(
                [Application::PARAM_CONTEXT => Manager::CONTEXT, Application::PARAM_ACTION => Manager::ACTION_VIEW_HOME]
            )
        );
    }

    public function getHomeService(): HomeService
    {
        return $this->getService(HomeService::class);
    }
}
