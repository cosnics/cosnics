<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Table\Entity\Group;

use Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Entry;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;

/**
 *
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Table\Entity
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class EntityTableCellRenderer
    extends \Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Table\Entity\EntityTable
{

    public function render_cell($column, $entity)
    {
        if ($column->get_name() == EntityTableColumnModel::PROPERTY_GROUP_MEMBERS)
        {
            return $this->getGroupMembers($entity);
        }

        return parent::render_cell($column, $entity);
    }

    /**
     *
     * @param array $group
     *
     * @return string
     */
    protected function getGroupMembers($group)
    {
        $entityId = $group[Entry::PROPERTY_ENTITY_ID];

        $limit = 21;

        $orderProperties = array();
        $orderProperties[] = new OrderBy(new PropertyConditionVariable(User::class_name(), User::PROPERTY_LASTNAME));
        $orderProperties[] = new OrderBy(new PropertyConditionVariable(User::class_name(), User::PROPERTY_FIRSTNAME));

        $userIds = $this->retrieveGroupUserIds($entityId);

        $condition = new InCondition(new PropertyConditionVariable(User::class_name(), User::PROPERTY_ID), $userIds);

        /** @var User[] $users */
        $users = \Chamilo\Core\User\Storage\DataManager::retrieves(
            User::class_name(),
            new DataClassRetrievesParameters($condition, $limit, null, $orderProperties))->as_array();

        if (count($users) == 0)
        {
            return null;
        }

        $exceedsLimit = false;

        if (count($users) == $limit)
        {
            $exceedsLimit = true;
            array_pop($users);
        }

        $html = array();
        $html[] = '<select style="width:180px">';

        foreach ($users as $user)
        {
            $html[] = '<option>' . $user->get_fullname() . '</option>';
        }

        if ($exceedsLimit)
        {
            $html[] = '<option>...</option>';
        }

        $html[] = '</select>';

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @see \Chamilo\Core\Repository\ContentObject\Assignment\Display\Table\Entity\EntityTableCellRenderer::isEntity()
     *
     * @param int $entityId
     * @param int $userId
     *
     * @return bool
     */
    protected function isEntity($entityId, $userId)
    {
        return $this->isGroupMember($this->getEntity($entityId), $userId);
    }

    /**
     *
     * @param DataClass $entity
     * @param integer $userId
     * @return boolean
     */
    protected function isGroupMember(DataClass $entity, $userId)
    {
        if ($this->isSubscribedInGroup($entity->getId(), $userId))
        {
            return true;
        }

        if ($this->hasChildren($entity))
        {
            return $this->isSubgroupMember($entity, $userId);
        }

        return false;
    }

    /**
     *
     * @param integer $groupId
     * @return integer[]
     */
    abstract protected function retrieveGroupUserIds($groupId);

    /**
     *
     * @param integer $entityId
     * @return \Chamilo\Libraries\Storage\DataClass\DataClass
     */
    abstract protected function getEntity($entityId);

    /**
     * @param DataClass $entity
     *
     * @return boolean
     */
    abstract protected function hasChildren($entity);

    /**
     *
     * @param DataClass $entity
     * @param integer $userId
     *
     * @return boolean
     */
    abstract protected function isSubgroupMember($entity, $userId);

    /**
     *
     * @param integer $groupId
     * @param integer $userId
     * @return boolean
     */
    abstract protected function isSubscribedInGroup($groupId, $userId);
}