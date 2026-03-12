<?php
namespace Chamilo\Core\Group;

use Chamilo\Core\Group\Architecture\Enum\ActionEnum;
use Chamilo\Core\Group\Service\GroupMembershipService;
use Chamilo\Core\Group\Service\GroupService;
use Chamilo\Core\Group\Service\GroupUrlGenerator;
use Chamilo\Core\User\Service\UserService;
use Chamilo\Libraries\Architecture\Domain\Application;
use Chamilo\Libraries\Architecture\Domain\ChamiloRequest;
use Chamilo\Libraries\Service\Routing\UrlGenerator;
use Chamilo\Libraries\UserInterface\Alert\Service\AlertsManager;
use Chamilo\Libraries\UserInterface\Breadcrumb\Architecture\Domain\BreadcrumbTrail;
use Chamilo\Libraries\UserInterface\Layout\Service\ApplicationHeaderRenderer;
use Chamilo\Libraries\UserInterface\Layout\Service\DefaultFooterRenderer;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\Group
 */
abstract class Manager extends Application
{
    public const string CONTEXT = __NAMESPACE__;
    public const string PARAM_GROUP_ID = 'group_id';
    public const string PARAM_RELATION_ID = 'relation_id';
    public const string PARAM_USER_ID = 'user_id';

    protected AlertsManager $alertsManager;

    protected BreadcrumbTrail $breadcrumbTrail;

    protected GroupMembershipService $groupMembershipService;

    protected GroupService $groupService;

    protected GroupUrlGenerator $groupUrlGenerator;

    protected UserService $userService;

    public function __construct(
        ChamiloRequest $request, ApplicationHeaderRenderer $applicationHeaderRenderer,
        DefaultFooterRenderer $defaultFooterRenderer, Translator $translator,
        GroupMembershipService $groupMembershipService, GroupUrlGenerator $groupUrlGenerator,
        AlertsManager $alertsManager, BreadcrumbTrail $breadcrumbTrail, GroupService $groupService,
        UserService $userService, UrlGenerator $urlGenerator
    )
    {
        parent::__construct($request, $applicationHeaderRenderer, $defaultFooterRenderer, $translator, $urlGenerator);

        $this->groupMembershipService = $groupMembershipService;
        $this->groupUrlGenerator = $groupUrlGenerator;
        $this->alertsManager = $alertsManager;
        $this->breadcrumbTrail = $breadcrumbTrail;
        $this->groupService = $groupService;
        $this->userService = $userService;
    }

    public function getAlertsManager(): AlertsManager
    {
        return $this->alertsManager;
    }

    public function getApplicationAction(): string
    {
        return ActionEnum::getActionValue(static::class);
    }

    public function getApplicationContext(): string
    {
        return self::CONTEXT;
    }

    public function getBreadcrumbTrail(): BreadcrumbTrail
    {
        return $this->breadcrumbTrail;
    }

    public function getDefaultApplicationAction(): string
    {
        return ActionEnum::BROWSE->value;
    }

    protected function getGroupMembershipService(): GroupMembershipService
    {
        return $this->groupMembershipService;
    }

    public function getGroupService(): GroupService
    {
        return $this->groupService;
    }

    public function getGroupUrlGenerator(): GroupUrlGenerator
    {
        return $this->groupUrlGenerator;
    }

    public function getUserService(): UserService
    {
        return $this->userService;
    }
}
