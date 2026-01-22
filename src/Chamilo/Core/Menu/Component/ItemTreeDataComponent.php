<?php
namespace Chamilo\Core\Menu\Component;

use Chamilo\Core\Menu\Manager;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Format\Tree\Menu\JsTreeMenuDataProvider;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * @package Chamilo\Core\Group\Component
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ItemTreeDataComponent extends Manager
{
    public function run(): Response
    {
        $urlFormat = $this->getUrlGenerator()->fromParameters(
            [
                Application::PARAM_CONTEXT => Manager::CONTEXT,
                Application::PARAM_ACTION => Manager::ACTION_BROWSE,
                Manager::PARAM_PARENT => '%s'
            ]
        );

        return new JsonResponse(
            data: $this->getJsTreeDataProvider()->getData(
                $urlFormat, $this->getCurrentParentIdentifier()
            )
        );
    }

    public function getCurrentParentIdentifier(): ?string
    {
        return $this->getRequest()->query->get(Manager::PARAM_PARENT);
    }

    /**
     * @param class-string<\Chamilo\Libraries\Format\Tree\Menu\JsTreeMenuDataProvider> $className
     */
    public function getJsTreeDataProvider(
        string $className = 'Chamilo\Core\Menu\UserInterface\Menu\ItemJsTreeMenuDataProvider'
    ): JsTreeMenuDataProvider
    {
        return $this->getService($className);
    }
}
