<?php
namespace Chamilo\Core\Group\Architecture\Enum;

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

/**
 * @package Chamilo\Core\Group\Architecture\Enum
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
enum ActionEnum: string
{
    case BROWSE = 'Browse';
    case BROWSE_NON_SUBSCRIBED_USERS = 'BrowseNonSubscribedUsers';
    case CREATE = 'Create';
    case DELETE = 'Delete';
    case GROUP_FEED = 'GroupFeed';
    case GROUP_TREE_DATA = 'GroupTreeData';
    case GROUP_XML_FEED = 'GroupXmlFeed';
    case MOVE = 'Move';
    case SUBSCRIBE = 'Subscribe';
    case TRUNCATE = 'Truncate';
    case UNSUBSCRIBE = 'Unsubscribe';
    case UPDATE = 'Update';

    public static function getAction(string $className): ActionEnum
    {
        return match ($className) {
            BrowseComponent::class => self::BROWSE,
            BrowseNonSubscribedUsersComponent::class => self::BROWSE_NON_SUBSCRIBED_USERS,
            CreateComponent::class => self::CREATE,
            DeleteComponent::class => self::DELETE,
            GroupFeedComponent::class => self::GROUP_FEED,
            GroupTreeDataComponent::class => self::GROUP_TREE_DATA,
            GroupXmlFeedComponent::class => self::GROUP_XML_FEED,
            MoveComponent::class => self::MOVE,
            SubscribeComponent::class => self::SUBSCRIBE,
            TruncateComponent::class => self::TRUNCATE,
            UnsubscribeComponent::class => self::UNSUBSCRIBE,
            UpdateComponent::class => self::UPDATE
        };
    }

    public static function getActionValue(string $className): string
    {
        return self::getAction($className)->value;
    }
}
