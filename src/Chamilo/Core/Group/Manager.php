<?php
namespace Chamilo\Core\Group;

use Chamilo\Core\Group\Component\BrowseComponent;
use Chamilo\Core\Group\Component\BrowseNonSubscribedUsersComponent;
use Chamilo\Core\Group\Component\CreateComponent;
use Chamilo\Core\Group\Component\DeleteComponent;
use Chamilo\Core\Group\Component\GroupFeedComponent;
use Chamilo\Core\Group\Component\GroupTreeDataComponent;
use Chamilo\Core\Group\Component\GroupXmlFeedComponent;
use Chamilo\Core\Group\Component\MoveComponent;
use Chamilo\Core\Group\Component\SubscribeComponent;
use Chamilo\Core\Group\Component\TruncateComponent;
use Chamilo\Core\Group\Component\UnsubscribeComponent;
use Chamilo\Core\Group\Component\UpdateComponent;
use Chamilo\Core\Group\Service\GroupMembershipService;
use Chamilo\Core\Group\Service\GroupUrlGenerator;
use Chamilo\Libraries\Architecture\Domain\Application;

/**
 * @package Chamilo\Core\Group
 */
abstract class Manager extends Application
{
    public const ACTION_BROWSE = 'Browse';
    public const ACTION_BROWSE_NON_SUBSCRIBED_USERS = 'BrowseNonSubscribedUsers';
    public const ACTION_CREATE = 'Create';
    public const ACTION_DELETE = 'Delete';
    public const ACTION_GROUP_FEED = 'GroupFeed';
    public const ACTION_GROUP_TREE_DATA = 'GroupTreeData';
    public const ACTION_GROUP_XML_FEED = 'GroupXmlFeed';
    public const ACTION_MOVE = 'Move';
    public const ACTION_SUBSCRIBE = 'Subscribe';
    public const ACTION_TRUNCATE = 'Truncate';
    public const ACTION_UNSUBSCRIBE = 'Unsubscribe';
    public const ACTION_UPDATE = 'Update';
    public const CONTEXT = __NAMESPACE__;
    public const PARAM_GROUP_ID = 'group_id';
    public const PARAM_RELATION_ID = 'relation_id';
    public const PARAM_USER_ID = 'user_id';

    public function getApplicationAction(): string
    {
        return match (static::class) {
            BrowseComponent::class => self::ACTION_BROWSE,
            BrowseNonSubscribedUsersComponent::class => self::ACTION_BROWSE_NON_SUBSCRIBED_USERS,
            CreateComponent::class => self::ACTION_CREATE,
            DeleteComponent::class => self::ACTION_DELETE,
            GroupFeedComponent::class => self::ACTION_GROUP_FEED,
            GroupTreeDataComponent::class => self::ACTION_GROUP_TREE_DATA,
            GroupXmlFeedComponent::class => self::ACTION_GROUP_XML_FEED,
            MoveComponent::class => self::ACTION_MOVE,
            SubscribeComponent::class => self::ACTION_SUBSCRIBE,
            TruncateComponent::class => self::ACTION_TRUNCATE,
            UnsubscribeComponent::class => self::ACTION_UNSUBSCRIBE,
            UpdateComponent::class => self::ACTION_UPDATE
        };
    }

    public function getApplicationContext(): string
    {
        return self::CONTEXT;
    }

    public function getDefaultApplicationAction(): string
    {
        return self::ACTION_BROWSE;
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
