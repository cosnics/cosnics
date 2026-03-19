<?php
namespace Chamilo\Core\Group\Component;

use Chamilo\Core\Group\Architecture\Enum\ActionEnum;
use Chamilo\Core\Group\Manager;
use Chamilo\Core\Group\Service\GroupMembershipService;
use Chamilo\Core\Group\Service\GroupService;
use Chamilo\Core\Group\Service\GroupUrlGenerator;
use Chamilo\Core\User\Service\UserService;
use Chamilo\Core\User\Storage\DataClass\User;
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

/**
 * @package Chamilo\Core\Group\Component
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class GroupTreeDataComponent extends Manager
{
    protected JsTreeMenuDataProvider $jsTreeMenuDataProvider;

    public function __construct(
        ChamiloRequest $request, ApplicationHeaderRenderer $applicationHeaderRenderer,
        DefaultFooterRenderer $defaultFooterRenderer, Translator $translator,
        GroupMembershipService $groupMembershipService, GroupUrlGenerator $groupUrlGenerator,
        AlertsManager $alertsManager, BreadcrumbTrail $breadcrumbTrail, GroupService $groupService,
        UserService $userService, UrlGenerator $urlGenerator, JsTreeMenuDataProvider $jsTreeMenuDataProvider
    )
    {
        parent::__construct(
            $request, $applicationHeaderRenderer, $defaultFooterRenderer, $translator, $groupMembershipService,
            $groupUrlGenerator, $alertsManager, $breadcrumbTrail, $groupService, $userService, $urlGenerator
        );

        $this->jsTreeMenuDataProvider = $jsTreeMenuDataProvider;
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
            data: $this->getJsTreeDataProvider()->getData(
                $urlFormat, $this->getCurrentGroupIdentifier()
            )
        );
    }

    public function getCurrentGroupIdentifier(): ?string
    {
        return $this->getRequest()->query->get(DataClass::PROPERTY_ID);
    }

    public function getJsTreeDataProvider(): JsTreeMenuDataProvider
    {
        return $this->jsTreeMenuDataProvider;
    }
}
