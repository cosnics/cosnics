<?php

namespace Chamilo\Application\Weblcms\Service\GroupEventListener;

use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\User\Storage\DataClass\User;

/**
 * @package Chamilo\Application\Weblcms\Service\GroupEventListenerInterface
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class GroupEventListener implements \Chamilo\Core\Group\Service\GroupEventListenerInterface
{

    /**
     * @param \Chamilo\Core\Group\Storage\DataClass\Group $group
     */
    public function afterCreate(Group $group)
    {
    }

    /**
     * @param \Chamilo\Core\Group\Storage\DataClass\Group $group
     */
    public function afterDelete(Group $group)
    {
        /**
         * Retrieve all parent groups of the group
         * Retrieve all (sub)groups that were impacted
         * Retrieve all users from all (sub)groups that were impacted
         * Retrieve all courses that were impacted (that have at least one of the subgroups or parent groups subscribed)
         *
         * For each course, determine the new course users, depending on the remaining subscriptions
         * Make a difference with all the users that are scheduled for deletion. Every user that is not subscribed anymore should be deleted
         *
         * Loop over every user that is not subscribed anymore
         * Remove them from the rights
         * Remove them from the course groups (with the course groups api so changes to extensions are pulled through)
         *
         * Remove the (sub)groups that were removed from the rights
         */
    }

    /**
     * @param \Chamilo\Core\Group\Storage\DataClass\Group $group
     * @param \Chamilo\Core\Group\Storage\DataClass\Group $oldParentGroup
     * @param \Chamilo\Core\Group\Storage\DataClass\Group $newParentGroup
     */
    public function afterMove(Group $group, Group $oldParentGroup, Group $newParentGroup)
    {
        // TODO: Implement afterMove() method.
    }

    /**
     * @param \Chamilo\Core\Group\Storage\DataClass\Group $group
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     */
    public function afterSubscribe(Group $group, User $user)
    {
    }

    /**
     * @param \Chamilo\Core\Group\Storage\DataClass\Group $group
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     */
    public function afterUnsubscribe(Group $group, User $user)
    {
        /**
         * Retrieve all parent groups of the group
         * Retrieve all courses that were impacted (that have at least one of parent groups (or the current group)
         *
         * For each course, determine the new course users, depending on the remaining subscriptions
         * Check if the user is still subscribed in the course
         *
         * If the user is not subscribed anymore:
         *
         * Remove them from the rights
         * Remove them from the course groups (with the course groups api so changes to extensions are pulled through)
         */
    }

    /**
     * @param \Chamilo\Core\Group\Storage\DataClass\Group $group
     */
    public function afterEmptyGroup(Group $group)
    {
        // TODO: Implement afterEmptyGroup() method.
    }
}

