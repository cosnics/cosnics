<?php
namespace Chamilo\Core\Menu\Component;

use Chamilo\Core\Menu\Manager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Domain\Application;
use Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAllowedException;
use Chamilo\Libraries\UserInterface\Tree\Service\JsTreeMenuDataProvider;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * @package Chamilo\Core\Group\Component
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ItemTreeDataComponent extends Manager
{
    /**
     * @throws \Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAllowedException
     */
    public function run(?User $currentUser = null): Response
    {
        if (!$currentUser instanceof User || !$currentUser->isPlatformAdministrator()) {
            throw new NotAllowedException();
        }

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
     * @param class-string<\Chamilo\Libraries\UserInterface\Tree\Service\JsTreeMenuDataProvider> $className
     */
    public function getJsTreeDataProvider(
        string $className = 'Chamilo\Core\Menu\UserInterface\Menu\ItemJsTreeMenuDataProvider'
    ): JsTreeMenuDataProvider
    {
        return $this->getService($className);
    }
}
