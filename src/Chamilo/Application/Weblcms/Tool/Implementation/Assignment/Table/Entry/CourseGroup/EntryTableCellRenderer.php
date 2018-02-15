<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Table\Entry\CourseGroup;

use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataManager;

/**
 *
 * @package Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Table\Entry\CourseGroup
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
        return \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataManager::retrieve_course_group_user_ids(
            $groupId);
    }

    /**
     * @param int $entityId
     *
     * @return \Chamilo\Libraries\Storage\DataClass\DataClass
     */
    protected function getEntity($entityId)
    {
        return DataManager::retrieve_by_id(CourseGroup::class_name(), $entityId);
    }

    /**
     * @param \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup $group
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
        return \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataManager::is_course_group_member(
            $groupId,
            $userId);
    }

    /**
     * @param \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup $entity
     *
     * @return boolean
     */
    protected function hasChildren($entity)
    {
        return $entity->has_children();
    }
}