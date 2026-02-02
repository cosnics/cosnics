<?php
namespace Chamilo\Core\Group;

use Chamilo\Core\Group\Service\GroupMembershipService;
use Chamilo\Core\Group\Service\GroupUrlGenerator;
use Chamilo\Core\User\Storage\DataClass\User;
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
    public const ACTION_VIEW = 'View';
    public const CONTEXT = __NAMESPACE__;
    public const DEFAULT_ACTION = self::ACTION_BROWSE;
    public const PARAM_GROUP_ID = 'group_id';
    public const PARAM_RELATION_ID = 'relation_id';
    public const PARAM_USER_ID = 'user_id';

    /**
     * @throws \Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAllowedException
     */
    public function __construct(?User $user = null)
    {
        parent::__construct($user);

        $this->checkAuthorization(Manager::CONTEXT);
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
