<?php
namespace Chamilo\Core\Group\Component;

use Chamilo\Core\Group\Architecture\Enum\ActionEnum;
use Chamilo\Core\Group\Manager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Interface\ApplicationInterface;
use Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAllowedException;
use Chamilo\Libraries\UserInterface\Tree\Service\JsTreeMenuDataProvider;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * @package Chamilo\Core\Group\Component
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class GroupTreeDataComponent extends Manager
{
    /**
     * @throws \Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAllowedException
     */
    public function run(?User $currentUser = null): Response
    {
        if (!$currentUser instanceof User) {
            throw new NotAllowedException();
        }

        $urlFormat = $this->getUrlGenerator()->fromParameters(
            [
                ApplicationInterface::PARAM_CONTEXT => Manager::CONTEXT,
                ApplicationInterface::PARAM_ACTION => ActionEnum::BROWSE->value,
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
