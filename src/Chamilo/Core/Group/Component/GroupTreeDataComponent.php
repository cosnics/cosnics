<?php
namespace Chamilo\Core\Group\Component;

use Chamilo\Core\Group\Architecture\Enum\ActionEnum;
use Chamilo\Core\Group\Manager;
use Chamilo\Core\Group\Service\GroupMembershipService;
use Chamilo\Core\Group\Service\GroupService;
use Chamilo\Core\Group\Service\GroupUrlGenerator;
use Chamilo\Core\User\Service\UserService;
use Chamilo\Core\User\Storage\Entity\User;
use Chamilo\Libraries\Architecture\Domain\ChamiloRequest;
use Chamilo\Libraries\Architecture\Interface\ApplicationInterface;
use Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAllowedException;
use Chamilo\Libraries\Service\Routing\UrlGenerator;
use Chamilo\Libraries\Storage\Architecture\Domain\DataClass;
use Chamilo\Libraries\UserInterface\Alert\Service\AlertsManager;
use Chamilo\Libraries\UserInterface\Breadcrumb\Architecture\Domain\BreadcrumbTrail;
use Chamilo\Libraries\UserInterface\Layout\Service\ApplicationHeaderRenderer;
use Chamilo\Libraries\UserInterface\Layout\Service\DefaultFooterRenderer;
use Chamilo\Libraries\UserInterface\Tree\Service\JsTreeMenuDataProvider;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\Translator;
use Symfony\Component\Uid\Uuid;

/**
 * @package Chamilo\Core\Group\Component
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class GroupTreeDataComponent extends Manager
{
    public function __construct(
        ChamiloRequest $request, ApplicationHeaderRenderer $applicationHeaderRenderer,
        DefaultFooterRenderer $defaultFooterRenderer, Translator $translator, UrlGenerator $urlGenerator,
        AlertsManager $alertsManager, BreadcrumbTrail $breadcrumbTrail, GroupMembershipService $groupMembershipService,
        GroupService $groupService, GroupUrlGenerator $groupUrlGenerator, UserService $userService,
        protected readonly JsTreeMenuDataProvider $jsTreeMenuDataProvider
    )
    {
        parent::__construct(
            $request, $applicationHeaderRenderer, $defaultFooterRenderer, $translator, $urlGenerator, $alertsManager,
            $breadcrumbTrail, $groupMembershipService, $groupService, $groupUrlGenerator, $userService
        );
    }

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
                DataClass::PROPERTY_ID => '%s'
            ]
        );

        return new JsonResponse(
            data: $this->jsTreeMenuDataProvider->getData(
                $urlFormat, $this->getCurrentGroupIdentifier()
            )
        );
    }

    public function getCurrentGroupIdentifier(): ?Uuid
    {
        $groupIdentifier = $this->getRequest()->query->get(DataClass::PROPERTY_ID);

        if ($groupIdentifier) {
            return Uuid::fromString($groupIdentifier);
        }
        else {
            return null;
        }
    }
}
