<?php
namespace Chamilo\Core\Group\Component;

use Chamilo\Core\Group\Manager;
use Chamilo\Libraries\Architecture\Domain\Application;
use Chamilo\Libraries\UserInterface\Tree\Service\JsTreeMenuDataProvider;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * @package Chamilo\Core\Group\Component
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class GroupTreeDataComponent extends Manager
{
    public function run(): Response
    {
        $urlFormat = $this->getUrlGenerator()->fromParameters(
            [
                Application::PARAM_CONTEXT => Manager::CONTEXT,
                Application::PARAM_ACTION => Manager::ACTION_BROWSE,
                Manager::PARAM_GROUP_ID => '%s'
            ]
        );

        return new JsonResponse(
            data: $this->getJsTreeDataProvider()->getData(
                $urlFormat, $this->getCurrentGroupIdentifier()
            )
        );
    }

    public function getCurrentGroupIdentifier(): ?string
    {
        return $this->getRequest()->query->get(Manager::PARAM_GROUP_ID);
    }

    /**
     * @param class-string<\Chamilo\Libraries\UserInterface\Tree\Service\JsTreeMenuDataProvider> $className
     */
    public function getJsTreeDataProvider(
        string $className = 'Chamilo\Core\Group\UserInterface\Menu\GroupJsTreeMenuDataProvider'
    ): JsTreeMenuDataProvider
    {
        return $this->getService($className);
    }
}
