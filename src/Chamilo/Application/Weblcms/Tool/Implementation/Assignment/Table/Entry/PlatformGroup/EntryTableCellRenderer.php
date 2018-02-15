<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Table\Entry\PlatformGroup;

use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\Group\Storage\DataManager;

/**
 *
 * @package Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Table\Entry\PlatformGroup
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class EntryTableCellRenderer extends \Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Table\Entry\Group\EntryTableCellRenderer
{
    /**
     *
     * @param integer $groupId
     *
     * @return int[]
     */
    protected function retrieveGroupUserIds($groupId)
    {
        $entity = $this->getEntity($groupId);
        return $entity->get_users(true, true);
    }

    /**
     * @param int $entityId
     *
     * @return Group | \Chamilo\Libraries\Storage\DataClass\DataClass
     */
    protected function getEntity($entityId)
    {
        return DataManager::retrieve_by_id(Group::class_name(), $entityId);
    }

    /**
     * @param \Chamilo\Core\Group\Storage\DataClass\Group $group
     * @param int $userId
     *
     * @return bool
     */
    protected function isSubgroupMember($group, $userId)
    {
        foreach ($group->get_children() as $subgroup)
        {
            if ($this->isGroupMember($subgroup, $userId))
            {
                return true;
            }
        }

        return false;
    }

    /**
     * @param int $groupId
     * @param int $userId
     *
     * @return bool
     */
    protected function isSubscribedInGroup($groupId, $userId)
    {
        return \Chamilo\Core\Group\Storage\DataManager::is_group_member($groupId, $userId);
    }

    /**
     * @param \Chamilo\Core\Group\Storage\DataClass\Group $entity
     *
     * @return boolean
     */
    protected function hasChildren($entity)
    {
        return $entity->has_children();
    }
}