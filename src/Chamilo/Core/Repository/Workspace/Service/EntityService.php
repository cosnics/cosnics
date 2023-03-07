<?php
namespace Chamilo\Core\Repository\Workspace\Service;

use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\Rights\Entity\PlatformGroupEntity;
use Chamilo\Core\Rights\Entity\UserEntity;
use Chamilo\Core\User\Storage\DataClass\User;

/**
 * @package Chamilo\Core\Repository\Workspace\Service
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class EntityService
{

    /**
     * @return int[][]
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function getEntitiesForGroup(Group $group): array
    {
        $entities = [];
        $entities[PlatformGroupEntity::ENTITY_TYPE] = [];

        $ancestorGroups = $group->get_ancestors();

        foreach ($ancestorGroups as $ancestorGroup)
        {
            $entities[PlatformGroupEntity::ENTITY_TYPE][] = $ancestorGroup->getId();
        }

        return $entities;
    }

    /**
     * @return int[][]
     */
    public function getEntitiesForUser(User $user): array
    {
        $entities = [];

        $entities[UserEntity::ENTITY_TYPE] = [$user->get_id()];
        $entities[PlatformGroupEntity::ENTITY_TYPE] = [];

        $userGroupIdentifiers = $user->get_groups(true);

        foreach ($userGroupIdentifiers as $userGroupIdentifier)
        {
            $entities[PlatformGroupEntity::ENTITY_TYPE][] = $userGroupIdentifier;
        }

        return $entities;
    }
}