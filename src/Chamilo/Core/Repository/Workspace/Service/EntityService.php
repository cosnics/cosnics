<?php
namespace Chamilo\Core\Repository\Workspace\Service;

use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\Rights\Entity\PlatformGroupEntity;
use Chamilo\Core\Rights\Entity\UserEntity;
use Chamilo\Core\User\Storage\DataClass\User;

/**
 *
 * @package Chamilo\Core\Repository\Workspace\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class EntityService
{

    /**
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @return integer[]
     */
    public function getEntitiesForUser(User $user)
    {
        $entities = array();

        $entities[UserEntity :: ENTITY_TYPE] = array($user->get_id());
        $entities[PlatformGroupEntity :: ENTITY_TYPE] = array();

        $userGroupIdentifiers = $user->get_groups(true);

        foreach ($userGroupIdentifiers as $userGroupIdentifier)
        {
            $entities[PlatformGroupEntity :: ENTITY_TYPE][] = $userGroupIdentifier;
        }

        return $entities;
    }

    /**
     *
     * @param \Chamilo\Core\Group\Storage\DataClass\Group $group
     * @return integer[]
     */
    public function getEntitiesForGroup(Group $group)
    {
        $entities = array();
        $entities[PlatformGroupEntity :: ENTITY_TYPE] = array();

        $ancestorGroups = $group->get_ancestors();

        while ($ancestorGroup = $ancestorGroups->next_result())
        {
            $entities[PlatformGroupEntity :: ENTITY_TYPE][] = $ancestorGroup->getId();
        }

        return $entities;
    }
}