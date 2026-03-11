<?php
namespace Chamilo\Core\Group;

use Chamilo\Core\Group\Architecture\Enum\ActionEnum;
use Chamilo\Core\Group\Service\GroupMembershipService;
use Chamilo\Core\Group\Service\GroupUrlGenerator;
use Chamilo\Libraries\Architecture\Domain\Application;

/**
 * @package Chamilo\Core\Group
 */
abstract class Manager extends Application
{
    public const string CONTEXT = __NAMESPACE__;
    public const string PARAM_GROUP_ID = 'group_id';
    public const string PARAM_RELATION_ID = 'relation_id';
    public const string PARAM_USER_ID = 'user_id';

    public function getApplicationAction(): string
    {
        return ActionEnum::getActionValue(static::class);
    }

    public function getApplicationContext(): string
    {
        return self::CONTEXT;
    }

    public function getDefaultApplicationAction(): string
    {
        return ActionEnum::BROWSE->value;
    }

    protected function getGroupMembershipService(): GroupMembershipService
    {
        return $this->getService(GroupMembershipService::class);
    }

    public function getGroupUrlGenerator(): GroupUrlGenerator
    {
        return $this->getService(GroupUrlGenerator::class);
    }
}
