<?php
namespace Chamilo\Core\Repository\Workspace\Service;

use Chamilo\Core\Group\Integration\Chamilo\Libraries\Rights\Service\GroupEntityProvider;
use Chamilo\Core\Group\Service\GroupsTreeTraverser;
use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\User\Integration\Chamilo\Libraries\Rights\Service\UserEntityProvider;
use Chamilo\Core\User\Storage\DataClass\User;

/**
 * @package Chamilo\Core\Repository\Workspace\Service
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class EntityService
{

    protected GroupsTreeTraverser $groupsTreeTraverser;

    public function __construct(GroupsTreeTraverser $groupsTreeTraverser)
    {
        $this->groupsTreeTraverser = $groupsTreeTraverser;
    }

    /**
     * @return int[][]
     */
    public function getEntitiesForGroup(Group $group): array
    {
        $entities = [];
        $entities[GroupEntityProvider::ENTITY_TYPE] = [];

        $ancestorGroups = $this->getGroupsTreeTraverser()->findParentGroupsForGroup($group);

        foreach ($ancestorGroups as $ancestorGroup)
        {
            $entities[GroupEntityProvider::ENTITY_TYPE][] = $ancestorGroup->getId();
        }

        return $entities;
    }

    /**
     * @return int[][]
     */
    public function getEntitiesForUser(User $user): array
    {
        $entities = [];

        $entities[UserEntityProvider::ENTITY_TYPE] = [$user->getId()];
        $entities[GroupEntityProvider::ENTITY_TYPE] = [];

        $userGroupIdentifiers = $user->get_groups(true);

        foreach ($userGroupIdentifiers as $userGroupIdentifier)
        {
            $entities[GroupEntityProvider::ENTITY_TYPE][] = $userGroupIdentifier;
        }

        return $entities;
    }

    public function getGroupsTreeTraverser(): GroupsTreeTraverser
    {
        return $this->groupsTreeTraverser;
    }
}